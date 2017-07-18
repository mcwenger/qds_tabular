<?php

/**
 * QDS Tabular
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Fieldtype
 * @author		Q Digital
 * @link
 */

class Qds_tabular_ft extends EE_Fieldtype {

  public $info = array(
		'name'     => 'QDS Tabular Data',
		'version'  => '1.0.0',
		'desc'     => '',
		'docs_url' => ''
	);

  var $has_array_data = TRUE;
  var $has_rows = TRUE;

  protected $_class;
  protected $_lower_class;
  protected $pkg = 'qds_tabular';
  protected $version = '1.0.0';
  protected $app_version;
  protected $ee_version;

  private $default_settings = array(
		'qds_tabular_cols' => '2',
		'qds_tabular_rows' => '2',
    'field_wide' => TRUE // for EE3
	);


  /**
	* Constructor function.
	*
	* @access  public
	* @return  void
	*/
	public function __construct()
	{
    parent::__construct();

    $this->_class       = get_class($this);
    $this->_lower_class = strtolower($this->_class);

    $app_version        = ee()->config->config['app_version'];
    $this->ee_version   = substr($app_version,0,1);
    $this->app_version  = $app_version;

    ee()->load->add_package_path(PATH_THIRD . $this->pkg);

		if (! isset(ee()->session->cache[$this->pkg]))
			ee()->session->cache[$this->pkg] = array();
    $this->cache =& ee()->session->cache[$this->pkg];
  }

  // --------------------------------------------------------------------

  /**
	 * Display field settings
	 *
	 * @param	array	field settings
	 * @return	string
	 */
	public function display_settings($settings = array())
	{
    return $this->{'_display_settings_ee' . $this->ee_version}($settings);
	}

  /**
	 * Display setting forms (ee2)
	 *
	 * @param	array	field settings
	 * @return	string
	 */
	private function _display_settings_ee2($settings = array(), $parent = FALSE)
	{
    $rows = $this->_field_settings_ee2($settings);
		foreach ($rows AS $row)
		{
			ee()->table->add_row($row);
		}
	}

  /**
	 * Display setting forms
	 *
	 * @param	array	field settings
	 * @return	string
	 */
  private function _display_settings_ee3($settings = array(), $parent = FALSE)
  {
    $output = array($this->pkg => array(
			'group' => $this->pkg,
			'label' => 'Tabular Options',
			'settings' => $this->_field_settings_ee3($settings)
		));
		return $output;
  }

  /**
	 * Return array with html for setting forms
	 *
	 * @param	array	field settings
	 * @return	string
	 */
  private function _field_settings_ee2($settings = array(), $parent = FALSE)
  {
    // build base array with defaults
    foreach ($this->default_settings AS $key => $val)
    {
      if (!array_key_exists($key, $settings))
      {
        $settings[$key] = $val;
      }
    }

    $fields = array();

    $qds_tabular_cols = array(
      'type'  => 'text',
      'name'  => 'qds_tabular_cols',
      'value' => $settings['qds_tabular_cols'],
      'class' => ($parent == 'matrix' ? 'matrix-textarea' : '')
    );
    $fields[] = array(
      '<strong>Starting Columns</strong>',
      form_input($qds_tabular_cols)
    );

    $qds_tabular_rows = array(
      'type'  => 'text',
      'name'  => 'qds_tabular_rows',
      'value' => $settings['qds_tabular_rows'],
      'class' => ($parent == 'matrix' ? 'matrix-textarea' : '')
    );
    $fields[] = array(
      '<strong>Starting Rows</strong>',
      form_input($qds_tabular_rows)
    );

    return $fields;
  }

  /**
	 * Return array with html for setting forms
	 *
	 * @param	array	field settings
	 * @return	string
	 */
  private function _field_settings_ee3($settings = array(), $parent = FALSE)
  {
    foreach ($this->default_settings as $key => $val)
		{
			if ( ! array_key_exists($key, $settings))
			{
				$settings[$key] = $val;
			}
		}

    return array(
      array(
        'title' => 'Starting Columns',
        'fields' => array(
          'qds_tabular_cols' => array(
            'type' => 'text',
            'value' => $settings['qds_tabular_cols'],
            'wrap' => TRUE
          )
        )
      ),
      array(
        'title' => 'Starting Columns',
        'fields' => array(
          'qds_tabular_rows' => array(
            'type' => 'text',
            'value' => $settings['qds_tabular_rows'],
            'wrap' => TRUE
          )
        )
      )
    );
  }

  /**
	 * Display field settings in Matrix field
	 */
	public function display_cell_settings($settings)
  {
    if ($this->ee_version == '2')
      return $this->_field_settings_ee2($settings, $parent = 'matrix');
    return FALSE;
  }

  /**
	 * Display field settings in Grid field
	 */
	public function grid_display_settings($settings)
  {
    $out      = array();
    $settings = $this->{'_field_settings_ee' . $this->ee_version}($settings, $parent = 'grid');

    if ($this->ee_version == '2')
    {
      foreach($settings as $si => $setting)
      {
        $out[$si] = $this->grid_settings_row(
          $setting[0],
          $setting[1]
        );
      }
      return $out;
    }
    else
    {
      return array('field_options' => $settings);
    }
  }

  /**
	 * Display field settings in Low Variable
	 */
	public function var_display_settings($settings)
  {
    return $this->{'_display_settings_ee' . $this->ee_version}($settings, $parent = 'lov_variables');
    // return $this->_display_settings($settings, $parent = 'lov_variables');
  }
  public function display_var_settings($settings)
  {
    return $this->{'_display_settings_ee' . $this->ee_version}($settings, $parent = 'lov_variables');
    // return $this->_display_settings($settings, $parent = 'lov_variables');
  }

  // --------------------------------------------------------------------

  /**
	 * Save field settings
	 *
	 * @access	public
	 * @return	array
	 */
	public function save_settings($data)
	{
    $settings = array();

		foreach ($this->default_settings AS $key => $val)
		{
      $post_val = (isset($data[$key]) ? $data[$key] : ee()->input->post($key));

			if (($settings[$key] = $post_val) === FALSE)
			{
				$settings[$key] = $val;
			}
		}
		return $settings;
	}

  /**
	 * Save field settings in Matrix field
	 */
	public function save_cell_settings($data)
  {
    return $this->save_settings($data);
  }

  /**
	 * Save field settings in Grid field
	 */
	public function grid_save_settings($data)
  {
    return $this->save_settings($data);
  }

  /**
	 * Save field settings in Low Variable
	 */
	public function var_save_settings($data)
  {
    return $this->save_settings($data);
  }
  public function save_var_settings($data)
  {
    return $this->save_settings($data);
  }

  // --------------------------------------------------------------------

  /**
	 * Display field in publish form
	 *
	 * @param	string	Current value for field
	 * @return	string	HTML containing input field
	 */
	public function display_field($data, $cell = FALSE)
  {
    $field_name = $cell ? (isset($this->cell_name) ? $this->cell_name : $this->field_name) : $this->field_name;
    $field_id   = preg_replace(array('/\[/', '/\]/'), array('_',''), $field_name);
    $col_id     = $cell ? (isset($this->col_id) ? $this->col_id : (isset($this->settings['col_id']) ? $this->settings['col_id'] : $this->field_name)) : FALSE;
    $data       = $this->_unpack( $data );
    $settings   = $data['settings'];
    $hasrows    = $data['hasrows'];

    // remove settings data from field data
    unset($data['settings']);
    unset($data['hasrows']);

    // include dependancy assets
    if ( ! isset($this->cache['dependencies_loaded']))
		{
			// include css and js
			$this->_include_theme_css('styles/' . $this->pkg . '.min.css');
			$this->_include_theme_js('scripts/' . $this->pkg . '.js');
			$this->cache['dependencies_loaded'] = TRUE;
		}

    $vars = array(
      'starting_cols'     => $this->settings['qds_tabular_cols'],
      'starting_rows'     => $this->settings['qds_tabular_rows'],
      'first_row_heading' => $settings['first_row_heading'],
      'first_col_heading' => $settings['first_col_heading'],
      'field_name'        => $field_name,
      'field_data'        => $data,
      'ee_version'        => $this->ee_version
    );

    // initialize the singular field (non-Grid or Matrix)
    if ( ! $cell && $field_name != '{DEFAULT}')
      $this->_insert_js('new QdsTabular("' . $field_name . '", "' . $field_id . '");');

    return ee()->load->view('display_field', $vars, true);
	}

  /**
	 * Display field in Matrix field
	 */
	public function display_cell($data, $cell = TRUE)
  {
    if ( ! isset($this->cache['matrix_bound']) )
		{
      $this->_insert_js('
        (function($) {
          Matrix.bind("qds_tabular", "display", function(cell){
            var $obj = $(this),
                $tabular = $(".ft-qds_tabular-container", $obj);
            $tabular.attr( "id", $tabular.attr("id").replace(/\[/g, "_").replace(/\]/g, "") );
            new QdsTabular(cell.field.id + "[" + cell.row.id + "][" + cell.col.id + "]", cell.field.id + "_" + cell.row.id + "_" + cell.col.id);
          });
        })(jQuery);
      ');
      $this->cache['matrix_bound'] = TRUE;
    }
    return $this->display_field($data, $cell);
  }

  /**
	 * Display field in Grid field
	 */
	public function grid_display_field($data, $cell = TRUE)
  {
    if ( ! isset($this->cache['grid_bound']) )
		{
      $this->_insert_js('
        (function($) {
          Grid.bind("qds_tabular", "display", function(cell, more){
            var $obj = $(cell),
                $tabular = $(".ft-qds_tabular-container", $obj),
                field_name = $("textarea:first", $tabular).attr("name").replace(/\[([0-9]{1,2})\]\[([0-9]{1,2})\]/, ""),
                field_id = field_name.replace(/\[/g, "_").replace(/\]/g, "");

            $tabular.attr( "id", "qds_tabular_" + field_id );
            new QdsTabular(field_name, field_id);
          });
        })(jQuery);
      ');
      $this->cache['grid_bound'] = TRUE;
    }
    return $this->display_field($data, $cell);
  }

  /**
	 * Display field in Low Variable
	 */
	public function var_display_field($data, $cell = FALSE)
  {
    if ( ! $this->var_id) return;
    return $this->display_field($data, $cell);
  }
  public function display_var_field($data, $cell = FALSE)
  {
    if ( ! $this->var_id) return;
    return $this->display_field($data, $cell);
  }

  // --------------------------------------------------------------------

  /**
	 * Save
	 *
	 * @param	mixed	 data
	 * @return	string	data to save to the database
	 */
  public function save( $data )
  {
    return $this->_pack( $data );
  }

  /**
	 * Save Matrix field
	 */
	public function save_cell( $data )
  {
    return $this->save( $data );
  }

  /**
	 * Save Grid field
	 */
	public function grid_save( $data )
  {
    return $this->save( $data );
  }

  /**
	 * Save Low Variable
	 */
	public function var_save( $data )
  {
    return $this->save( $data );
  }
  public function save_var_field( $data )
  {
    return $this->save( $data );
  }

  // --------------------------------------------------------------------

  /**
	* Process data before handing off to replace_tag
	*
	* @access      public
	* @param       string    Current value for field
	* @param       array     Tag parameters
	* @param       bool
	* @return      string
	*/
  public function pre_process($data)
  {
    return $this->_tagpair( $this->_unpack($data) );
  }

  // --------------------------------------------------------------------

	/**
	* Display tag in template
	*
	* @access      public
	* @param       string    Current value for field
	* @param       array     Tag parameters
	* @param       bool
	* @return      string
	*/
	public function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
    // echo $this->field_name."\n";
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // echo '--------------------';

    if ( ! $this->has_rows)
      return FALSE;

    // print_r($tagdata);
    // print_r($data);

    // echo ee()->TMPL->parse_variables($tagdata, $data);
    // echo '---------------------------';
    // echo '---------------------------';

    return ee()->TMPL->parse_variables($tagdata, $data);

    // return ee()->api_channel_fields->get_pair_field(
		// 	$tagdata,
		// 	'table',
		// 	'');

  }

  // var_display_tag($data)

  // --------------------------------------------------------------------

  /**
	 * Returns true if content type is accepted.
	 *
	 * @param string $name
	 * @return bool $result
	 */
	public function accepts_content_type($name)
	{
		return in_array($name, array(
      'channel',
      'grid',
      // 'low_variables',
      'blocks/1'
    ));
	}

  // --------------------------------------------------------------------

  /**
   * Theme URL
   */
  private function _theme_url()
  {
    if (! isset($this->cache['theme_url']))
    {
      $theme_folder_url = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : ee()->config->slash_item('theme_folder_url').'third_party/';
      $this->cache['theme_url'] = $theme_folder_url . $this->pkg . '/';
    }
    return $this->cache['theme_url'];
  }

  /**
   * Include Theme CSS
   */
  private function _include_theme_css($file)
  {
    ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'?' . $this->version . '" />');
  }

  /**
   * Include Theme JS
   */
  private function _include_theme_js($file)
  {
    ee()->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'?' . $this->version . '"></script>');
  }

  // --------------------------------------------------------------------

	/**
	 * Insert CSS
	 */
	private function _insert_css($css)
	{
		ee()->cp->add_to_head('<style type="text/css">'.$css.'</style>');
	}

	/**
	 * Insert JS
	 */
	private function _insert_js($js)
	{
		ee()->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
	}

  // --------------------------------------------------------------------

  /**
	 * Pack
	 *
	 * @param	array	 data
	 * @return	string	data to save to the database
	 */
  public function _pack( $data )
  {
    $out         = array();
    $settings    = isset($data['settings']) ? $data['settings'] : array();
    $hasrows     = FALSE;
    $hassettings = FALSE;

		if ( ! is_array($data))
			return base64_encode(serialize($out));

    if (isset($data['settings']))
      unset($data['settings']);

    // check for data
    foreach($data as $ri => $row)
    {
      foreach($row as $ri => $coldata)
      {
        if ($coldata != '') $hasrows = TRUE;
        break;
      }
    }

    // check for settings
    foreach($settings as $setting)
    {
      if ($setting) $hassettings = TRUE;
      break;
    }

    // if we don't have data, empty the database record
    if ( ! $hasrows && ! $hassettings) return FALSE;

    // add the settings back
    $data['settings'] = $settings;
    $data['hasrows']  = $hasrows;

    return base64_encode(serialize($data));
  }

  // --------------------------------------------------------------------

  /**
	 * Unpack
	 *
	 * @param	string  data returned from the databse
	 * @return	string  unpacked data
	 */
  public function _unpack( $data )
  {
    return unserialize(base64_decode($data));
  }

  // --------------------------------------------------------------------

  /**
	 * Create tagpair vars
	 *
	 * @param	array  unpacked array
	 * @return	string  unpacked data
	 */
  public function _tagpair( $data )
  {
    $prefix = 'table:';
    $settings = $data['settings'];
    $hasrows  = $data['hasrows'];

    $this->has_rows = (bool) $hasrows;

    // should we parse the tagdata
    if ( ! (bool) $hasrows)
    {
      $this->has_array_data = FALSE;
      return FALSE;
    }

    // fallback structure
    $out = array(
      array(
        $prefix . 'first_row_heading' => (bool) $settings['first_row_heading'],
        $prefix . 'first_col_heading' => (bool) $settings['first_col_heading'],
        $prefix . 'rows' => array(
          array(
            $prefix . 'row_count' => 0,
            $prefix . 'total_rows' => 0,
            $prefix . 'columns' => array(
              array(
                $prefix . 'col_count' => 0,
                $prefix . 'total_cols' => 0,
                $prefix . 'value' => 0
              )
            )
          )
        )
      )
    );

    // if we don't have data, empty the database record
    if (empty($data)) return $out;

    // remove settings data from field data
    unset($data['settings']);
    unset($data['hasrows']);

    foreach($data as $ri => $row)
    {
      foreach($row as $ci => $coldata)
      {
        $out[0][$prefix . 'rows'][$ri][$prefix . 'row_count'] = $ri+1;
        $out[0][$prefix . 'rows'][$ri][$prefix . 'total_rows'] = count($data);
        $out[0][$prefix . 'rows'][$ri][$prefix . 'columns'][$ci] = array(
          $prefix . 'value' => $coldata,
          $prefix . 'col_count' => $ci+1,
          $prefix . 'total_cols' => count($row)
        );
      }
    }
    return $out;
  }

  // --------------------------------------------------------------------

}// end: class
