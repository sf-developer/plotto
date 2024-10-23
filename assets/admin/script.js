jQuery(function($) {
    const { __ } = wp.i18n;

    /**
     * Initialize notiflix
     */
    Notiflix.Notify.init({
        width: '300px',
        distance: '30px',
        position: 'center-bottom',
        closeButton: false,
        fontSize: '16px',
        messageMaxLength: 320,
    });

	$(document).on("click", ".save-lottery", function (e) {
		e.preventDefault();
		let $this = $(this),
			prizes = [],
			error = false;
		if( $(".repeater-prizes > .row").children().length === 0 ) {
			Notiflix.Notify.warning(__("No prizes added", "plotto"));
		} else {
			$(".repeater-prizes .repeater-prizes-item").each(function(k, v) {
				let id = $(this).data("id"),
					blockCoordination = $("input[name='prizes[" + k + "][block_coordination]']").val(),
					bonuseCoordination = $("input[name='prizes[" + k + "][bonuse_coordination]']").val(),
					prize = $("input[name='prizes[" + k + "][prize]']").val();
				if( $.trim(blockCoordination).length > 0 && $.trim(bonuseCoordination).length > 0 && $.trim(prize).length > 0 ) {
					prizes.push({
						id: id,
						blockCoordination: blockCoordination,
						bonuseCoordination: bonuseCoordination,
						prize: prize
					});
				} else {
					error = true;
					Notiflix.Notify.warning(__("Please fill in all fields for prize", "plotto"));
				}
			});
		}

		if( prizes.length > 0 && !error ) {
			$.ajax({
				type: "POST",
				url: plotto_ajax.url,
				data: {
					"action": "save_lottery",
					"name": $("#lottery-name").val(),
					"content": tinymce.get('lottery-content').getContent(),
					"color": $("#lottery-color option:selected").val(),
					"company": $("#lottery-company option:selected").val(),
					"logo": $("#lottery-company option:selected").attr("data-logo"),
					"totalPrice": $("#lottery-total-price").val(),
					"prizeCurrency": $("#prize-currency option:selected").val(),
					"ticketPrice": $("#lottery-ticket-price").val(),
					"blockCount": $("#lottery-block-count").val(),
					"choosenBlockCount": $("#lottery-choosen-block").val(),
					"bonuseCount": $("#lottery-bonuse-count").val(),
					"choosenBonuseCount": $("#lottery-choosen-bonuse").val(),
					"expireTime": $("#lottery-expire-time").val(),
					"fakeParticipant": $("#lottery-fake-participant").val(),
					"isUpdate": $this.data("pid") === 0 ? false : true,
					"lotteryId": $this.data("pid"),
					"prizes": prizes,
					"security": plotto_ajax.nonce
				},
				beforeSend: function() {
					Notiflix.Loading.standard(__("Saving Lottery...", "plotto"));
				},
				success: function (response) {
					Notiflix.Loading.remove();
					if(response.success) {
						Notiflix.Notify.success(response.data.message);
						if(response.data.url) {
							window.location.href = response.data.url;
						} else {
							$(".repeater-prizes > .row").html(response.data.html);
						}
					} else {
						Notiflix.Notify.failure(response.data.message);
					}
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					var errorMessage = xhr.status + ': ' + xhr.responseText
					Notiflix.Loading.remove();
					Notiflix.Notify.failure(errorMessage);
				}
			});
		}
	});

	// Runs when the media button is clicked.
	$(document).on("click", ".upload-company-logo", function(e) {
		e.preventDefault();
		let companyLogoFrame,
			btn = $(this); // Get the btn

		if ( !btn ) return; // Check if it's the upload button

		// Sets up the media library frame
		companyLogoFrame = wp.media.frames.companyLogoFrame = wp.media({
			title: __("Choose or Upload a Company Logo", "plotto"),
			button: { text:  __("Use Logo", "plotto") },
		});

		// Runs when an image is selected.
		companyLogoFrame.on('select', function() {
			var companyLogoAttachment = companyLogoFrame.state().get('selection').first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$(".company-logo-preview img").attr("src", companyLogoAttachment.url);
			$("#company-logo-url").val(companyLogoAttachment.url);
			$("#company-logo-id").val(companyLogoAttachment.id);

		});
		// Opens the media library frame.
		companyLogoFrame.open();
	});

	$(document).on("click", ".save-company", function (e) {
		e.preventDefault();
		let $this = $(this);
		$.ajax({
			type: "POST",
			url: plotto_ajax.url,
			data: {
				"action": "save_company",
				"name": $("#company-name").val(),
				"content": tinymce.get('company-content').getContent(),
				"logo": $("#company-logo-id").val(),
				"isUpdate": $this.data("cid") === 0 ? false : true,
				"companyId": $this.data("cid"),
				"security": plotto_ajax.nonce
			},
			beforeSend: function() {
				Notiflix.Loading.standard(__("Saving Company...", "plotto"));
			},
			success: function (response) {
				Notiflix.Loading.remove();
				if(response.success) {
					Notiflix.Notify.success(response.data.message);
					if(response.data.url) {
						window.location.href = response.data.url;
					}
				} else {
					Notiflix.Notify.failure(response.data.message);
				}
			},
			error: function(xhr, status, error) {
				var errorMessage = xhr.status + ': ' + xhr.statusText
				Notiflix.Loading.remove();
				Notiflix.Notify.failure(errorMessage);
			}
		});
	});

	if($(".repeater-prizes")[0]) {
		$(".repeater-prizes").repeater({
			isFirstItemUndeletable: true,
			initEmpty: false,
			show: function () {
				$(this).attr("data-id", 0);
				$(this).slideDown();
				easyNumberSeparator({
					selector: '.number-separator',
				})
			},
			hide: function (deleteElement) {
				let $this = $(this);
				Notiflix.Confirm.show(
                    __( 'Confirm deletion', 'plotto' ),
                    __( 'Are you sure you want to delete this item?', 'plotto' ),
                    __( 'Yes', 'plotto' ),
                    __( 'No', 'plotto' ),
                    function okCb() {
                        if($this.data("id") == 0){
                            $this.slideUp(deleteElement);
                        }else{
                            $.ajax({
                                type: "POST",
                                url: plotto_ajax.url,
                                data: {
                                    "action": "delete_prize",
                                    "id": $this.data("id"),
									"lotteryId": $this.data("lid"),
                                    "security": plotto_ajax.nonce
                                },
                                beforeSend: function() {
                                    Notiflix.Loading.standard(__("Deleting Prize...", "plotto"));
                                },
                                success: function (response) {
                                    Notiflix.Loading.remove();
                                    if(response.success) {
                                        $(".repeater-prizes > .row").html(response.data.html);
                                        Notiflix.Notify.success(response.data.message);
                                    }else {
                                        Notiflix.Notify.warning(response.data.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    var errorMessage = xhr.status + ': ' + xhr.statusText
									Notiflix.Loading.remove();
									Notiflix.Notify.failure(errorMessage);
                                }
                            });
                        }
                    },
                    function cancelCb() {}
                );
			}
		});
	}
	if($(".number-separator")[0]) {
		easyNumberSeparator({
			selector: '.number-separator',
		})
	}
})