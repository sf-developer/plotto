jQuery(function($) {
    const { __ } = wp.i18n;

    $(document).on("click", ".plot-load-previouse-lotteries", function() {
      const id = $(this).data("lot-id");
      const date = $(this).find(".text-truncate").text();
      $.ajax({
        type: "POST",
        url: plotto_ajax.url,
        data: {
          action: "get_previouse_lotteries",
          id: id,
          date: date,
          security: plotto_ajax.nonce
        },
        success: function (response) {
          let resp = JSON.parse(response);
          $("#plot-previous-lotteries").html(resp.html);
        },
        error: function(xhr, status, error) {
          var errorMessage = xhr.status + ': ' + xhr.statusText
          console.log(errorMessage)
        }
      });
    });

    $(document).on("click", ".plot-save-request-withdrawal", function() {
      const type = $("#payment-type").val();
      const amount = $("#amount").val();
      const account = type === 'tether' ? $("#tether-wallet").val() : type === 'perfect' ? $("#perfect-account").val() : $("#card-no").val() + "||" + $("#iban").val();
      $.ajax({
        type: "POST",
        url: plotto_ajax.url,
        data: {
          action: "request_new_withdrawal",
          type: type,
          amount: amount,
          account: account,
          security: plotto_ajax.nonce
        },
        success: function (response) {
          let resp = JSON.parse(response);
          console.log(resp);
          $("#requestWithdrawalModal").modal("hide");
        },
        error: function(xhr, status, error) {
          var errorMessage = xhr.status + ': ' + xhr.statusText
          console.log(errorMessage)
        }
      });
    });

    if($("#tickets")[0]) {
        ticketsDatatable = $("#tickets").DataTable({
          ajax: {
            url: plotto_ajax.url,
            type: "POST",
            data: {
              action: "get_tickets",
              security: plotto_ajax.nonce
            }
          },
          order: [[0, 'desc']],
          processing: true,
          serverSide: true,
          responsive: true,
          buttons: ["excelHtml5"]
        });
    }

    if($("#withdrawal")[0]) {
        ticketsDatatable = $("#withdrawal").DataTable({
          ajax: {
            url: plotto_ajax.url,
            type: "POST",
            data: {
              action: "get_withdrawals",
              security: plotto_ajax.nonce
            }
          },
          order: [[0, 'desc']],
          processing: true,
          serverSide: true,
          responsive: true,
          buttons: ["excelHtml5"]
        });
    }

    // Set up FlipDown
    if($("#plot-flipdown")[0]) {
      var flipdown = new FlipDown(Date.parse(document.getElementById("plot-flipdown").getAttribute("data-time")) / 1000, "plot-flipdown")
        .start()
        .ifEnded(() => {
          console.log(__("The countdown has ended!", "plotto"));
      });
    }


    const root = document.documentElement;
    const dropdownTitle = document.querySelector(".dropdown-title");
    const dropdownList = document.querySelector(".dropdown-list");

    const setDropdownProps = (deg, ht, opacity) => {
      root.style.setProperty("--rotate-arrow", deg !== 0 ? deg + "deg" : 0);
      root.style.setProperty("--dropdown-height", ht !== 0 ? ht + "rem" : 0);
      root.style.setProperty("--list-opacity", opacity);
    };

    $(document).on("click", ".main-button", function() {
      const listWrapperSizes = 3.5; // margins, paddings & borders
      const dropdownOpenHeight = 4.6 * dropdownList.length + listWrapperSizes;
      const currDropdownHeight =
        root.style.getPropertyValue("--dropdown-height") || "0";

      currDropdownHeight === "0"
        ? setDropdownProps(180, dropdownOpenHeight, 1)
        : setDropdownProps(0, 0, 0);
    })

    $(document).on("click", ".dropdown-list", function(e) {
      const clickedItemText = e.target.innerText.toLowerCase().trim();

      dropdownTitle.innerHTML = clickedItemText;
      setDropdownProps(0, 0, 0);
    })

    $(document).on("change", "#payment-type", function() {
      if($(this).val() === 'perfect') {
        $(".if-tether").addClass("d-none");
        $(".if-account").addClass("d-none");
        $(".if-perfect").removeClass("d-none");
      }else if($(this).val() === 'tether') {
        $(".if-tether").removeClass("d-none");
        $(".if-account").addClass("d-none");
        $(".if-perfect").addClass("d-none");
      } else {
        $(".if-tether").addClass("d-none");
        $(".if-account").removeClass("d-none");
        $(".if-perfect").addClass("d-none");
      }
    })
});