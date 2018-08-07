var table;

var NDdataTable = function() {

    var handleNDdataTable = function(data) {

      var obj = JSON.parse(data);

      if(obj.htmlTable) {

      // draw table
      $('#ndtable-portlet').html(obj.htmlTable);

      // init data table
      table = $('#nd-table').DataTable({

        //scrollY: "450px",
        scrollX:        true,
        scrollCollapse: true,
        //paging:false,
        fixedColumns:   {
            leftColumns: 1,
        },
        "lengthMenu": [[10,20],[10,20]],
        "order": [[ 0, "asc" ]],

      });

      // trick for drawing table properly (small bug with fixedColumns plugin)
      setTimeout(function(){
        table.search( "", true, false, true ).draw();
        // fit fixed column height
        $(".DTFC_LeftHeadWrapper table thead tr th").height($(".dataTables_scrollHeadInner table thead").height() - 20);
      }, 100);

    } else {
      $('#ndtable-portlet').html("No data provided");
    }

    }

    var handleToggleColumns = function(data) {

      var obj = JSON.parse(data);

      if(obj.htmlTable) {

      var columns = '';
      var countCol = 1;
      var countRow = 0;
      var rowIds = [];
      var rowState = [];
      // draw toggle buttons
      $.each(obj.structure, function(k, v) {
          columns += '<div class="col-md-12 col-sm-12 r-toggle-cols"><p>Show / Hide columns for <strong>' + k + '</strong></p>';
          var arrauxid = [];
          var arrauxst = [];
          var cc = 0;
          // foreach button: [row, col] and column (matching with column id in the table)
          $.each(v, function(k1, v1) {
            arrauxid.push(countCol);
            arrauxst.push(true);
            columns += '<button type="button" class="btn green btn-xs toggle-vis" data-r="' + countRow + '"  data-c="' + cc + '" data-column="' + countCol + '">' + k1 + '</button> ';
            countCol ++;
            cc ++;
          });
          columns += ' <button type="button" class="btn green btn-xs toggle-all" data-r="' + countRow + '">All</button>';
          columns += '</div>';
          rowIds[countRow] = arrauxid;
          rowState[countRow] = arrauxst;
          countRow ++;
      });

      $('#nd-toggle-table').html(columns);
      $('#nd-toggle-table').show();

      // buttons toggle column
      $('button.toggle-vis').on( 'click', function (e) {
          e.preventDefault();
          // Get the column API object
          var col = parseInt($(this).attr('data-c'));
          var row = parseInt($(this).attr('data-r'));
          var column = table.column($(this).attr('data-column'));
          // Toggle the visibility
          column.visible( ! column.visible() );
          // fit fixed column height
          $(".DTFC_LeftHeadWrapper table thead tr th").height($(".dataTables_scrollHeadInner table thead").height() - 20);

          // if enabled, disable, if disabled, enable
          if($(this).hasClass("green")) {
            rowState[row][col] = false;
            $(this).removeClass("green");
            $(this).addClass("default");
            // if all disabled, disable All button
            if($.inArray(true, rowState[row]) === -1) {
              $("button.toggle-all[data-r='" + row + "']").removeClass("green");
              $("button.toggle-all[data-r='" + row + "']").addClass("default");
            }
          } else {
            rowState[row][col] = true;
            $(this).addClass("green");
            $(this).removeClass("default");
            // if all enabled, enable All button
            if($.inArray(false, rowState[row]) === -1) {
              $("button.toggle-all[data-r='" + row + "']").addClass("green");
              $("button.toggle-all[data-r='" + row + "']").removeClass("default");
            }
          }

      } );

      // buttons toggle all columns of a block
      $('button.toggle-all').on( 'click', function (e) {
        var arrButtons = rowIds[$(this).attr('data-r')];

        // if enabled, disable, if disabled, enable
        if($(this).hasClass("green")) {
          $(this).removeClass("green");
          $(this).addClass("default");
          // disable all buttons of the row
          $.each(arrButtons, function(k,v) {
            table.column(v).visible(false);
            $("button.toggle-vis[data-column='" + v + "']").removeClass("green");
            $("button.toggle-vis[data-column='" + v + "']").addClass("default");
          });
        } else {
          $(this).addClass("green");
          $(this).removeClass("default");
          // enable all buttons of the row
          $.each(arrButtons, function(k,v) {
            table.column(v).visible(true);
            $("button.toggle-vis[data-column='" + v + "']").addClass("green");
            $("button.toggle-vis[data-column='" + v + "']").removeClass("default");
          });
        }

        // fit fixed column height
        $(".DTFC_LeftHeadWrapper table thead tr th").height($(".dataTables_scrollHeadInner table thead").height() - 20);

      });

      }

    }

    var handleFilters = function(data) {

      var obj = JSON.parse(data);

      if(!$.isEmptyObject(obj.filters)){

      var filters = '';
      // draw filters
      $.each(obj.filters, function(k, v) {
          filters += '<div class="col-md-3 col-sm-3 col-filter">Filter <strong>' + k + ':</strong><br>' +
          '<div class="selector">' + v + '</div>' +
          '</div>'
      });

      $('#nd-filters-table').html(filters);
      $('#nd-filters-table').show();

      // FILTERS
      // selects
      $('#nd-filters-table .col-filter').children().each(function(index,element) {
        if($( this ).hasClass("selector")){
          var col = parseInt($(element)[0].innerText);
          var column = table.column(col);
          var select = $('<select style="width: 100%!important;" class="selector form-control input-sm input-xsmall input-inline"><option value="">All</option></select>');
          column.data().unique().sort().each( function ( d, j ) {
            if((d.length) && (d != '&nbsp;')){
              var sel = '';
              select.append( '<option value="'+d+'" ' + sel +'>'+d+'</option>' );
            }
          } );
          $(this).html(select);

          $(this)
            .on( 'change', function () {
              var rgx = $(this).find("select").val();
              var val = $.fn.dataTable.util.escapeRegex(
                rgx
              );
              var match = '^' + val + '$';

            column
            .search( val ? match : '', true, false )
            .draw();
          } );
        }
      });

      }

    }

    return {
        //main function to initiate the module
        init: function(data) {

            handleNDdataTable(data);
            handleFilters(data);
            handleToggleColumns(data);

        }

    };

}();

$(document).ready(function() {

  $.ajax({

    type: "POST",
    //url: "https://mmb.irbbarcelona.org/webdev/test/tables/nd_table.php",
    url: "tools/nucldynwf/genes_stats.php",
    data: "tmpf=" + $("#tmpf").val(),
    success: function(data) {

      NDdataTable.init(data);

    }

  });

});

