var QdsTabular;

(function($) {

  var $document = $(document);
  var $body = $(document.body);

  /**
   * QdsTabular
   */
  QdsTabular = function(field_name, field_id) {

    // keep a record of this object
    // QdsTabular.instances = [];
    // QdsTabular.instances.push(this);

    var _ = this;
    _.id = field_id;
    _.name = field_name;
    _.rows = [];
    _.total_rows = 0;
    _.total_cols = 0;
    _.max_rows = 100;
    _.max_cols = 15;

    var $obj = $('#qds_tabular_' + _.id),
        $table = $('> table', $obj),
        $tbody = $('> tbody', $table),
        $thead = $('> thead', $table),
        $add_row = $('.btn-add-row', $obj),
        $add_col = $('.btn-add-col', $obj);

    _.tmplCell = function(row_index, col_index) {
      var out = ' <td class="cell">'
              + '   <textarea name="' + _.name + '[' + (row_index - 1) + '][' + col_index + ']" data-row="' + (row_index - 1) + '" data-cell="' + col_index + '"></textarea>'
              + ' </td>';
      return out;
    };

    _.tmplRow = function(index) {
      var out = ''
        + '<tr data-index="' + (index - 1) + '">'
        + ' <th>'
        + '   <div class="row-count">' + index + '</div>'
        + '   <a href="#" class="delete-row"><span aria-hidden="true">×</span></a>'
        + ' </th>';
      for (i=0; i < _.total_cols; i++)
      {
        out += _.tmplCell(index, i);
      }
      out += '</tr>';
      return out;
    };

    _.tmplCol = function(index) {
      var out = ''
        + '<th class="col-count" data-index="' + (index - 1) + '">'
        + '  <div class="count">' + index + '</div>'
        + '  <a href="#" class="delete-col"><span aria-hidden="true">&times;</span></a>'
        + '</th>';
      return out;
    };

    _.initRows = function(){
      $('> tr', $tbody).each(function(i){
        var index = $(this).attr('data-index'),
            row = {'index': index, 'obj': $(this)};
        _.rows.push(row);
      });
      _.total_rows = _.rows.length;
    };

    _.initCols = function(){
      _.total_cols = $('tr:first td', $tbody).length;
    };

    _.reIndex = function() {
      $('th.col-count', $thead).each(function(i) {
        $(this)
          .attr('data-index', i)
          .find('.count').html(i+1);
      });
      var col_index = 0,
          row_index = -1;
      $('tr td', $tbody).each(function(i) {
        if (col_index == _.total_cols) col_index = 0;
        if (col_index == 0) row_index++;

        var $cell = $(this)
            $row = $cell.parent(),
            $fields = $('input, textarea, select', $cell);

        $row.attr('data-index', row_index);
        $('.row-count', $row).html(row_index+1);

        $fields.each(function(i) {
          var name = $(this).attr('name'),
              regex = new RegExp(_.name.replace(/\[/g, '\\[').replace(/\]/g, '\\]') + '\\[([0-9]{1,2})\\]\\[([0-9]{1,2})\\]'),
              name_new = name.replace(regex, _.name + '[' + row_index + '][' + col_index + ']');
          $(this)
            .attr('name', name_new)
            .attr('data-row', row_index)
            .attr('data-cell', col_index);
        });
        col_index++;
      });
    };

    _.addRow = function() {
      console.log('clicked');
      if (_.total_rows < _.max_rows)
      {
        _.total_rows = _.total_rows + 1;
        $tbody.append( _.tmplRow( _.total_rows ) );

        if (_.total_rows == _.max_rows) $add_row.addClass('js-disabled');
      }
    };

    _.removeRow = function(e) {
      e.preventDefault();

      if (_.total_rows > 1)
      {
        _.total_rows = _.total_rows - 1;

        var $obj = $(this),
            $row = $obj.closest('tr');

        $row.remove();
        _.reIndex();
      }
      if (_.total_rows < _.max_rows) $add_row.removeClass('js-disabled');
    };

    _.addCol = function() {
      if (_.total_cols < _.max_cols)
      {
        _.total_cols = _.total_cols + 1;
        $('tr', $thead).append( _.tmplCol( _.total_cols ) );
        $('> tr', $tbody).each(function(i){
          $(this).append( _.tmplCell( i + 1, _.total_cols - 1 ) );
        });

        if (_.total_cols == _.max_cols) $add_col.addClass('js-disabled');
      }
    };

    _.removeCol = function(e) {
      e.preventDefault();

      if (_.total_cols > 1)
      {
        _.total_cols = _.total_cols - 1;

        var $obj = $(this),
            $th = $obj.closest('th'),
            index = $th.attr('data-index');

        $th.remove();
        $('> tr', $tbody).each(function(i){
          $('> td', $(this)).each(function(i){
            if (i == index) $(this).remove();
          });
        });
        _.reIndex();
      }
      if (_.total_cols < _.max_cols) $add_col.removeClass('js-disabled');
    };

    _.bindEvents = function() {
      console.log('bound', field_name);

      $add_row.on('click', _.addRow);
      $add_col.on('click', _.addCol);
      $table.on('click', '.delete-row', _.removeRow);
      $thead.on('click', '.delete-col', _.removeCol);
      $tbody.sortable({
        handle: "th",
        placeholder: "drag-placeholder",
        forcePlaceholderSize: !0,
        axis: "y",
        containment: "#qds_tabular_" + _.id + ' table',
        update: _.reIndex
      })
    };

    _.init = function() {
      _.initRows();
      _.initCols();
      _.bindEvents();
    }

    // load up
    _.init();
  };

})(jQuery);
