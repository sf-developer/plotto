jQuery(function($){
  const { __ } = wp.i18n;
  let lotteriesDatatable, companiesDatatable;

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

    if( $("#chart-sales")[0] ) {
      let optionsSales = {
        chart: {
          type: 'bar',
          height: 150,
          width: '100%',
          stacked: true,
          foreColor: '#fff',
        },
        plotOptions: {
          bar: {
            dataLabels: {
              enabled: false
            },
            columnWidth: '75%',
            endingShape: 'rounded'
          }
        },
        dataLabels: {
          enabled: false
        },
        colors: ["#75baff"],
        series: [{
          name: __("Sales", "plotto"),
          data: [0,0,0,0,0,0,0]
        }],
        labels: [__("Mon", "plotto"), __("Tue", "plotto"), __("Wed", "plotto"), __("Thu", "plotto"), __("Fri", "plotto"), __("Sat", "plotto"), __("Sun", "plotto")],
        xaxis: {
          axisBorder: {
            show: true
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            show: false
          },
          labels: {
            show: true,
            style: {
              fontSize: '11px'
            }
          },
        },
        grid: {
          xaxis: {
            lines: {
              show: false
            },
          },
          yaxis: {
            lines: {
              show: false
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: true
          },
          labels: {
            show: false
          },
        },
        legend: {
          floating: true,
          position: 'top',
          horizontalAlign: 'right',
          offsetY: -36
        },
        tooltip: {
          shared: true,
          intersect: false
        }
      },
      chartSales = new ApexCharts(
        document.querySelector("#chart-sales"),
        optionsSales
      );
      chartSales.render();

      $.getJSON({
        url: plotto_ajax.url,
        type: "POST",
        data: {
          action: "get_weekly_sales_amount",
          security: plotto_ajax.nonce
        }
      }, function(response) {
        $("#chart-sales-total").html(response.total)
        chartSales.updateSeries([{
          name: __("Sales", "plotto"),
          data: response.data
        }])
      })
    }

    if( $("#chart-ticket-sold")[0] ) {
      let optionsTicketSold = {
        chart: {
          type: 'bar',
          height: 150,
          width: '100%',
          stacked: true,
          foreColor: '#fff',
        },
        plotOptions: {
          bar: {
            dataLabels: {
              enabled: false
            },
            columnWidth: '75%',
            endingShape: 'rounded'
          }
        },
        dataLabels: {
          enabled: false
        },
        colors: ["#024b91"],
        series: [{
          name: __("Ticket sold", "plotto"),
          data: [0,0,0,0,0,0,0]
        }],
        labels: [__("Mon", "plotto"), __("Tue", "plotto"), __("Wed", "plotto"), __("Thu", "plotto"), __("Fri", "plotto"), __("Sat", "plotto"), __("Sun", "plotto")],
        xaxis: {
          axisBorder: {
            show: true
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            show: false
          },
          labels: {
            show: true,
            style: {
              fontSize: '11px'
            }
          },
        },
        grid: {
          xaxis: {
            lines: {
              show: false
            },
          },
          yaxis: {
            lines: {
              show: false
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: true
          },
          labels: {
            show: false
          },
        },
        legend: {
          floating: true,
          position: 'top',
          horizontalAlign: 'right',
          offsetY: -36
        },
        tooltip: {
          shared: true,
          intersect: false
        }
      },
      chartTicketSold = new ApexCharts(
        document.querySelector("#chart-ticket-sold"),
        optionsTicketSold
      );
      chartTicketSold.render();

      $.getJSON({
        url: plotto_ajax.url,
        type: "POST",
        data: {
          action: "get_weekly_sales",
          security: plotto_ajax.nonce
        }
      }, function(response) {
        $("#chart-sales-count-total").html(response.total)
        chartTicketSold.updateSeries([{
          name: __("Ticket sold", "plotto"),
          data: response.data
        }])
      })
    }

    if( $("#chart-loosers")[0] ) {
      let optionsLotteries = {
        chart: {
          type: 'bar',
          height: 150,
          width: '100%',
          stacked: true,
          foreColor: '#000',
        },
        plotOptions: {
          bar: {
            dataLabels: {
              enabled: false
            },
            columnWidth: '75%',
            endingShape: 'rounded'
          }
        },
        dataLabels: {
          enabled: false
        },
        colors: ["#af7800"],
        series: [{
          name: __("Loosers", "plotto"),
          data: [0,0,0,0,0,0,0]
        }],
        labels: [__("Mon", "plotto"), __("Tue", "plotto"), __("Wed", "plotto"), __("Thu", "plotto"), __("Fri", "plotto"), __("Sat", "plotto"), __("Sun", "plotto")],
        xaxis: {
          axisBorder: {
            show: true
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            show: false
          },
          labels: {
            show: true,
            style: {
              fontSize: '11px'
            }
          },
        },
        grid: {
          xaxis: {
            lines: {
              show: false
            },
          },
          yaxis: {
            lines: {
              show: false
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: true
          },
          labels: {
            show: false
          },
        },
        legend: {
          floating: true,
          position: 'top',
          horizontalAlign: 'right',
          offsetY: -36
        },
        tooltip: {
          shared: true,
          intersect: false
        }
      },
      chartLoosers = new ApexCharts(
        document.querySelector("#chart-loosers"),
        optionsLotteries
      );
      chartLoosers.render();

      $.getJSON({
        url: plotto_ajax.url,
        type: "POST",
        data: {
          action: "get_weekly_loosers",
          security: plotto_ajax.nonce
        }
      }, function(response) {
        $("#chart-loosers-count-total").html(response.total)
        chartLoosers.updateSeries([{
          name: __("Loosers", "plotto"),
          data: response.data
        }])
      })
    }

    if( $("#chart-winners")[0] ) {
      let optionsWinners = {
        chart: {
          type: 'bar',
          height: 150,
          width: '100%',
          stacked: true,
          foreColor: '#fff',
        },
        plotOptions: {
          bar: {
            dataLabels: {
              enabled: false
            },
            columnWidth: '75%',
            endingShape: 'rounded'
          }
        },
        dataLabels: {
          enabled: false
        },
        colors: ["#750000"],
        series: [{
          name: __("Winners", "plotto"),
          data: [0,0,0,0,0,0,0]
        }],
        labels: [__("Mon", "plotto"), __("Tue", "plotto"), __("Wed", "plotto"), __("Thu", "plotto"), __("Fri", "plotto"), __("Sat", "plotto"), __("Sun", "plotto")],
        xaxis: {
          axisBorder: {
            show: true
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            show: false
          },
          labels: {
            show: true,
            style: {
              fontSize: '11px'
            }
          },
        },
        grid: {
          xaxis: {
            lines: {
              show: false
            },
          },
          yaxis: {
            lines: {
              show: false
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: true
          },
          labels: {
            show: false
          },
        },
        legend: {
          floating: true,
          position: 'top',
          horizontalAlign: 'right',
          offsetY: -36
        },
        tooltip: {
          shared: true,
          intersect: false
        }
      },
      chartWinners = new ApexCharts(
        document.querySelector("#chart-winners"),
        optionsWinners
      );
      chartWinners.render();

      $.getJSON({
        url: plotto_ajax.url,
        type: "POST",
        data: {
          action: "get_weekly_winners",
          security: plotto_ajax.nonce
        }
      }, function(response) {
        $("#chart-winners-count-total").html(response.total)
        chartWinners.updateSeries([{
          name: __("Winners", "plotto"),
          data: response.data
        }])
      })
    }

    if( $("#chart-profile-visit")[0] ) {
      let optionsProfileVisit = {
        annotations: {
          position: "back",
        },
        dataLabels: {
          enabled: false,
        },
        chart: {
          type: "bar",
          height: 300,
        },
        fill: {
          opacity: 1,
        },
        plotOptions: {},
        series: [
          {
            name: __("Sales", "plotto"),
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          },
        ],
        colors: "#435ebe",
        xaxis: {
          categories: [
            __("Jan", "plotto"),
            __("Feb", "plotto"),
            __("Mar", "plotto"),
            __("Apr", "plotto"),
            __("May", "plotto"),
            __("Jun", "plotto"),
            __("Jul", "plotto"),
            __("Aug", "plotto"),
            __("Sep", "plotto"),
            __("Oct", "plotto"),
            __("Nov", "plotto"),
            __("Dec", "plotto")
          ],
        },
      },
      chartProfileVisit = new ApexCharts(
        document.querySelector("#chart-profile-visit"),
        optionsProfileVisit
      );
      chartProfileVisit.render();

      $.getJSON({
        url: plotto_ajax.url,
        type: "POST",
        data: {
          action: "get_each_month_sales",
          security: plotto_ajax.nonce
        }
      }, function(response) {
        chartProfileVisit.updateSeries([{
          name: __("Sales", "plotto"),
          data: response
        }])
      })
    }

    if( $("#plot-total-sales")[0] && $("#plot-total-users")[0] && $("#plot-total-winners")[0] )
    {
      $.ajax({
        type: "POST",
        url: plotto_ajax.url,
        data: {
          action: "get_today_reports",
          security: plotto_ajax.nonce
        },
        success: function (response) {
          let resp = JSON.parse(response);
          $("#plot-total-sales").text(resp.total_sale);
          $("#plot-total-users").text(resp.total_users);
          $("#plot-total-winners").text(resp.total_winners);
        },
        error: function(xhr, status, error) {
          var errorMessage = xhr.status + ': ' + xhr.statusText
          console.log(errorMessage)
        }
      });
    }

    // if( $("#chart-europe")[0] ) {
    //   var optionsEurope = {
    //     series: [
    //       {
    //         name: "series1",
    //         data: [310, 800, 600, 430, 540, 340, 605, 805, 430, 540, 340, 605],
    //       },
    //     ],
    //     chart: {
    //       height: 80,
    //       type: "area",
    //       toolbar: {
    //         show: false,
    //       },
    //     },
    //     colors: ["#5350e9"],
    //     stroke: {
    //       width: 2,
    //     },
    //     grid: {
    //       show: false,
    //     },
    //     dataLabels: {
    //       enabled: false,
    //     },
    //     xaxis: {
    //       type: "datetime",
    //       categories: [
    //         "2018-09-19T00:00:00.000Z",
    //         "2018-09-19T01:30:00.000Z",
    //         "2018-09-19T02:30:00.000Z",
    //         "2018-09-19T03:30:00.000Z",
    //         "2018-09-19T04:30:00.000Z",
    //         "2018-09-19T05:30:00.000Z",
    //         "2018-09-19T06:30:00.000Z",
    //         "2018-09-19T07:30:00.000Z",
    //         "2018-09-19T08:30:00.000Z",
    //         "2018-09-19T09:30:00.000Z",
    //         "2018-09-19T10:30:00.000Z",
    //         "2018-09-19T11:30:00.000Z",
    //       ],
    //       axisBorder: {
    //         show: false,
    //       },
    //       axisTicks: {
    //         show: false,
    //       },
    //       labels: {
    //         show: false,
    //       },
    //     },
    //     show: false,
    //     yaxis: {
    //       labels: {
    //         show: false,
    //       },
    //     },
    //     tooltip: {
    //       x: {
    //         format: "dd/MM/yy HH:mm",
    //       },
    //     },
    //   },
    //   chartEurope = new ApexCharts(
    //     document.querySelector("#chart-europe"),
    //     optionsEurope
    //   );
    //   chartEurope.render()
    // }

    // if( $("#chart-america")[0] ) {
    //   let optionsAmerica = {
    //     ...optionsEurope,
    //     colors: ["#008b75"],
    //   },
    //   chartAmerica = new ApexCharts(
    //     document.querySelector("#chart-america"),
    //     optionsAmerica
    //   );
    //   chartAmerica.render()
    // }

    // if( $("#chart-indonesia")[0] ) {
    //   let optionsIndonesia = {
    //     ...optionsEurope,
    //     colors: ["#dc3545"],
    //   },
    //   chartIndonesia = new ApexCharts(
    //     document.querySelector("#chart-indonesia"),
    //     optionsIndonesia
    //   );
    //   chartIndonesia.render()
    // }

    if($("#lotteries")[0]) {
      lotteriesDatatable = $("#lotteries").DataTable({
        ajax: {
          url: plotto_ajax.url,
          type: "POST",
          data: {
            action: "get_lotteries",
            security: plotto_ajax.nonce
          }
        },
        order: [[0, 'desc']],
        processing: true,
        serverSide: true,
        responsive: true,
        buttons: ["excelHtml5"]
      });

      lotteriesDatatable
      .on('order.dt search.dt', function () {
        let i = 1;
        lotteriesDatatable
          .cells(null, 0, { search: 'applied', order: 'applied' })
          .every(function (cell) {
            this.data(i++);
        });
      })
      .draw();
    }

    if($("#companies")[0]) {
      companiesDatatable = $("#companies").DataTable({
        ajax: {
          url: plotto_ajax.url,
          type: "POST",
          data: {
            action: "get_companies",
            security: plotto_ajax.nonce
          }
        },
        order: [[0, 'desc']],
        columnDefs: [
          {
            data: null,
            defaultContent: '<button type"button" class="btn btn-sm btn-danger w-100 mb-1 delete-company" data-id="">' + __( 'Delete', 'plotto' ) + '</button><a href"" class="btn btn-sm w-100 mb-1 btn-primary">' + __( 'Edit', 'plotto' ) + '</a>',
            targets: -1,
            orderable: !1,
            visible: !0,
          }
        ],
        createdRow: function (t, e, n) {
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          $(t).find("td:eq(-1) a").attr("href", window.location.origin + window.location.pathname + "?action=plot-dashboard&p=add-company&cid=" + e[0] + "&_plot_nonce=" + urlParams.get("_plot_nonce")),
          $(t).find("td:eq(-1) .delete-company").attr("data-id", e[0]);
        },
        processing: true,
        serverSide: true,
        responsive: true,
        buttons: ["excelHtml5"]
      });

      companiesDatatable
      .on('order.dt search.dt', function () {
        let i = 1;
        companiesDatatable
          .cells(null, 0, { search: 'applied', order: 'applied' })
          .every(function (cell) {
            this.data(i++);
        });
      })
      .draw();
    }

    if($("#participants")[0]) {
      participantsDatatable = $("#participants").DataTable({
        columns: [
          { data: 'participant_id', searchable: false, orderable: true },
          { data: 'user_id' },
          { data: 'username' },
          { data: 'user_email' },
          { data: 'lottery_id' },
          { data: 'buy_ticket_date' },
          { data: 'choosen_blocks_bonuses' },
          { data: 'lottery_answer' },
          { data: 'lottery_answer_date' },
          { data: 'pay_to_user' },
          { data: 'is_winner' },
          { data: 'win_amount' },
          { data: 'total_amount' },
          { data: 'ticket_price' },
          { data: 'block_coordination' },
          { data: 'bonuse_coordination' }
        ],
        ajax: {
          url: plotto_ajax.url,
          type: "POST",
          data: {
            action: "get_participants",
            security: plotto_ajax.nonce
          }
        },
        order: [[0, 'desc']],
        processing: true,
        serverSide: true,
        responsive: true,
        initComplete: function() {
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const lot_id = urlParams.get("lot_id");
          if(lot_id) {
            this
            .api()
            .column(4)
            .search(lot_id)
            .draw()
          }
        },
        buttons: ["excelHtml5"]
      });

      participantsDatatable
      .on('order.dt search.dt', function () {
        let i = 1;
        participantsDatatable
          .cells(null, 0, { search: 'applied', order: 'applied' })
          .every(function (cell) {
            this.data(i++);
        });
      })
      .draw();

      $('.filter-checkbox').on('change', function(e){
        if($(this).is(":checked")) {
          let winners = $(this).val();
          participantsDatatable.column(10).search(winners).draw();
        } else {
          participantsDatatable.columns(10).search("").draw();
        }
      });

      $('.status-dropdown').on('change', function(e){
        let status = $(this).val();
        $('.status-dropdown').val(status)
        participantsDatatable.column(9).search(status).draw();
      })
    }

    $(document).on("click", ".show-winner-confirmation", function (e) {
      e.preventDefault();
      $("#winner-modal .approve-winner-modal").attr("data-winner_id", $(this).data("winner_id"));
      $("#winner-modal .approve-winner-modal").attr("data-participant_id", $(this).data("participant_id"));
      $("#winner-modal .reject-winner-modal").attr("data-participant_id", $(this).data("participant_id"));
      $("#winner-modal .reject-winner-modal").attr("data-winner_id", $(this).data("winner_id"));
      $("#winner-modal").modal("show");
    });

    $(document).on("click", ".approve-winner-modal", function (e) {
      e.preventDefault();
      $("#winner-modal").modal("hide");
      let $this = $(this);
      Notiflix.Confirm.show(
        __('Approving Confirm', 'plotto'),
        __('Do you want to approve this winner?', 'plotto'),
        __('Yes', 'plotto'),
        __('No', 'plotto'),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              "action": "approve_winner",
              "winner_id": $this.data("winner_id"),
              "participant_id": $this.data("participant_id"),
              "security": plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Approving Winner...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                Notiflix.Notify.success(response.data.message);
                participantsDatatable.ajax.reload();
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
        },
        function cancelCb() {}
      );
    });

    $(document).on("click", ".reject-winner-modal", function (e) {
      e.preventDefault();
      $("#winner-modal").modal("hide");
      let $this = $(this);
      Notiflix.Confirm.prompt(
        __('Rejecting Confirm', 'plotto'),
        __('Do you want to reject this winner?', 'plotto'),
        '',
        __('Yes', 'plotto'),
        __('No', 'plotto'),
        (clientAnswer) => {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              "action": "reject_winner",
              "winner_id": $this.data("winner_id"),
              "participant_id": $this.data("participant_id"),
              "note": clientAnswer,
              "security": plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Rejecting Winner...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                Notiflix.Notify.success(response.data.message);
                participantsDatatable.ajax.reload();
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
        },
        () => {}
      );
    });

    if($("#withdrawal-requests")[0]) {
      withdrawalRequestsDatatable = $("#withdrawal-requests").DataTable({
        columns: [
          { data: 'request_id', searchable: false, orderable: true },
          { data: 'username' },
          { data: 'date' },
          { data: 'amount' },
          { data: 'type' },
          { data: 'account' },
          { data: 'status' }
        ],
        ajax: {
          url: plotto_ajax.url,
          type: "POST",
          data: {
            action: "get_withdrawal_requests",
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

    $(document).on("click", ".show-withdrawal-confirmation", function (e) {
      e.preventDefault();
      $("#action-modal .approve-withdrawal-modal").attr("data-request_id", $(this).data("request_id"));
      $("#action-modal .reject-withdrawal-modal").attr("data-request_id", $(this).data("request_id"));
      $("#action-modal").modal("show");
    });

    $(document).on("click", ".approve-withdrawal-modal", function (e) {
      e.preventDefault();
      $("#action-modal").modal("hide");
      let $this = $(this);
      Notiflix.Confirm.show(
        __('Approving Confirm', 'plotto'),
        __('Do you want to approve this withdrawal request?', 'plotto'),
        __('Yes', 'plotto'),
        __('No', 'plotto'),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              "action": "approve_withdrawal_request",
              "request_id": $this.data("request_id"),
              "security": plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Approving Request...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                Notiflix.Notify.success(response.data.message);
                withdrawalRequestsDatatable.ajax.reload();
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
        },
        function cancelCb() {}
      );
    });

    $(document).on("click", ".reject-withdrawal-modal", function (e) {
      e.preventDefault();
      $("#action-modal").modal("hide");
      let $this = $(this);
      Notiflix.Confirm.prompt(
        __('Rejecting Confirm', 'plotto'),
        __('Do you want to reject this request?', 'plotto'),
        '',
        __('Yes', 'plotto'),
        __('No', 'plotto'),
        (clientAnswer) => {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              "action": "reject_withdrawal_request",
              "request_id": $this.data("request_id"),
              "note": clientAnswer,
              "security": plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Rejecting Request...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                Notiflix.Notify.success(response.data.message);
                withdrawalRequestsDatatable.ajax.reload();
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
        },
        () => {}
      );
    });

    if($("#reports")[0]) {
      reportsDatatable = $("#reports").DataTable({
        ajax: {
          url: plotto_ajax.url,
          type: "POST",
          data: {
            action: "get_logs",
            security: plotto_ajax.nonce
          }
        },
        columnDefs: [
          {
              searchable: false,
              orderable: false,
              targets: 0
          }
        ],
        order: [[3, 'desc']],
        processing: true,
        serverSide: true,
        responsive: true,
        buttons: ["excelHtml5"]
      });

      reportsDatatable
        .on('order.dt search.dt', function () {
          let i = 1;
          reportsDatatable
            .cells(null, 0, { search: 'applied', order: 'applied' })
            .every(function (cell) {
              this.data(i++);
          });
        })
        .draw();
    }

    $(document).on("click", ".finish-lottery", function(e) {
      e.preventDefault();
      let id = $(this).data("id");
      Notiflix.Confirm.show(
        __("Finish lottery", "plotto"),
        __("Do you want to finish this lottery?", "plotto"),
        __("Yes", "plotto"),
        __("No", "plotto"),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              action: "prepare_finish_modal",
              id: id,
              security: plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Getting Data...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                $("#lottery-modal-label").text(__("Finish lottery #", "plotto") + id);
                $("#lottery-modal .modal-body").html(response.data.html);
                $("#lottery-modal .save-lottery-modal").attr("data-id", id);
                $("#lottery-modal .save-lottery-modal").attr("data-type", "finish");
                $("#lottery-modal").modal("show");
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
        },
        function cancelCb() {}
      );
    })

    $(document).on("click", ".renew-lottery", function(e) {
      e.preventDefault();
      let id = $(this).data("id");
      Notiflix.Confirm.show(
        __("Renew lottery", "plotto"),
        __("Do you want to renew this lottery?", "plotto"),
        __("Yes", "plotto"),
        __("No", "plotto"),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              action: "prepare_renew_modal",
              id: id,
              security: plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Getting Data...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                $("#lottery-modal-label").text(__("Renew lottery #", "plotto") + id);
                $("#lottery-modal .modal-body").html(response.data.html);
                $("#lottery-modal .save-lottery-modal").attr("data-id", id);
                $("#lottery-modal .save-lottery-modal").attr("data-type", "renew");
                $("#lottery-modal").modal("show");
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
        },
        function cancelCb() {}
      );
    })

    $(document).on("click", ".save-lottery-modal", function(e) {
      e.preventDefault();
      let id = $(this).attr("data-id"),
          type = $(this).attr("data-type");
      if(type === "finish") {
        Notiflix.Confirm.show(
          __("Finish lottery", "plotto"),
          __("Do you want to finish this lottery?", "plotto"),
          __("Yes", "plotto"),
          __("No", "plotto"),
          function okCb() {
            $.ajax({
              type: "POST",
              url: plotto_ajax.url,
              data: {
                action: "finish_lottery",
                id: id,
                winBlockCode: $("#win-block-code").val(),
                winBonuseCode: $("#win-bonuse-code").val(),
                security: plotto_ajax.nonce
              },
              beforeSend: function() {
                Notiflix.Loading.standard(__("Updating Lottery...", "plotto"));
              },
              success: function (response) {
                Notiflix.Loading.remove();
                if(response.success) {
                  Notiflix.Notify.success(response.data.message);
                  $("#lottery-modal .modal-body").html("");
                  $("#lottery-modal-label").text("");
                  $("#lottery-modal .save-lottery-modal").removeAttr("id").removeAttr("type");
                  $("#lottery-modal").modal("hide");
                  lotteriesDatatable.ajax.reload(null, false);
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
          },
          function cancelCb() {}
        );
      } else {
        Notiflix.Confirm.show(
          __("Renew lottery", "plotto"),
          __("Do you want to renew this lottery?", "plotto"),
          __("Yes", "plotto"),
          __("No", "plotto"),
          function okCb() {
            $.ajax({
              type: "POST",
              url: plotto_ajax.url,
              data: {
                action: "renew_lottery",
                id: id,
                expireTime: $("#expire-time").val(),
                security: plotto_ajax.nonce
              },
              beforeSend: function() {
                Notiflix.Loading.standard(__("Updating Lottery...", "plotto"));
              },
              success: function (response) {
                Notiflix.Loading.remove();
                if(response.success) {
                  Notiflix.Notify.success(response.data.message);
                  $("#lottery-modal .modal-body").html("");
                  $("#lottery-modal-label").text("");
                  $("#lottery-modal .save-lottery-modal").removeAttr("id").removeAttr("type");
                  $("#lottery-modal").modal("hide");
                  lotteriesDatatable.ajax.reload(null, false);
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
          },
          function cancelCb() {}
        );
      }
    })

    $(document).on("click", ".delete-lottery", function(e) {
      e.preventDefault();
      let id = $(this).data("id");
      Notiflix.Confirm.show(
        __("Delete lottery", "plotto"),
        __("Do you want to delete this lottery?", "plotto"),
        __("Yes", "plotto"),
        __("No", "plotto"),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              action: "delete_lottery",
              id: id,
              security: plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Deleting Lottery...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                lotteriesDatatable.ajax.reload(null, false);
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
        },
        function cancelCb() {}
      );
    })

    $(document).on("click", ".delete-company", function(e) {
      e.preventDefault();
      let id = $(this).data("id");
      Notiflix.Confirm.show(
        __("Delete company", "plotto"),
        __("Do you want to delete this company?", "plotto"),
        __("Yes", "plotto"),
        __("No", "plotto"),
        function okCb() {
          $.ajax({
            type: "POST",
            url: plotto_ajax.url,
            data: {
              action: "delete_company",
              id: id,
              security: plotto_ajax.nonce
            },
            beforeSend: function() {
              Notiflix.Loading.standard(__("Deleting Company...", "plotto"));
            },
            success: function (response) {
              Notiflix.Loading.remove();
              if(response.success) {
                companiesDatatable.ajax.reload(null, false);
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
        },
        function cancelCb() {}
      );
    })
  });