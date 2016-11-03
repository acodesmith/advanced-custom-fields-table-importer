<?php
$acf_active       = is_plugin_active( 'advanced-custom-fields-pro/acf.php' );
$acf_table_active = is_plugin_active( 'advanced-custom-fields-table-field/acf-table.php' );
$passed = $acf_active && $acf_table_active;
?>
<div class="acf-table-import wrap">
  <h1>Advanced Custom Fields: Table Import</h1>
  <?php if( !$acf_active ): ?>
  <div class="notice notice-error">
    <p><?= _e('Advanced Custom Fields Pro is Required!'); ?></p>
  </div>
  <?php endif; ?>
  <?php if( !$acf_table_active ): ?>
  <div class="notice notice-error">
    <p><?= _e('Advanced Custom Fields: Table Field is Required!'); ?></p>
  </div>
  <?php endif; ?>
  <?php if( !$passed ): ?>
    <div class="stuffbox">
      <div class="inside">
        <h2>Advanced Custom Fields: Table Import</h2>
        <p>
          Advanced Custom Fields Table Importer requires two other plugins. Please visit the WordPress plugin repository for more information.
        </p>
      </div>
    </div>
  <?php else:
    //All required plugs-ins are loaded, let's get the party started.
    ?>
    <?php if( !empty( $_GET['notification'] )): ?>
    <div class="notice notice-success">
      <p><?= _e($_GET['notification']); ?></p>
    </div>
    <?php endif; ?>
    <?php if( !empty( $_GET['error'] )): ?>
    <div class="notice notice-error">
      <p><?= _e($_GET['error']); ?></p>
    </div>
    <?php endif; ?>
    <div class="notice notice-info">
      <p><?= _e('The importer currently only supports Comma Separated Value documents, aka CSV.'); ?></p>
    </div>
    <div id="poststuff">
      <div class="stuffbox">
        <div class="inside">
          <form id="acf_ti_form"
                method="POST"
                action="<?php echo admin_url( 'admin.php' ); ?>"
                enctype="multipart/form-data">
            <input type="hidden" name="action" value="acf_ti" />
            <fieldset>
              <legend class="edit-comment-author">Import</legend>
              <table class="form-table editcomment">
                <tbody>
                <tr>
                  <td class="first"><label for="acf_ti_file">Select File to Import:</label></td>
                  <td>
                    <input type="file"
                           accept=".csv"
                           name="acf_ti_file"
                           id="acf_ti_file"
                           required>
                  </td>
                </tr>
                <tr>
                  <td class="first"><label for="acf_ti_delimiter">Choose Delimiter:</label></td>
                  <td>
                    <select name="acf_ti_delimiter" id="acf_ti_delimiter">
                      <option value=",">,</option>
                      <option value=";">;</option>
                      <option value="|">|</option>
                      <option value="^">^</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="first"><label for="acf_ti_delimiter">Remove Empty Values?</label></td>
                  <td>
                    <select name="acf_ti_array_filter" id="acf_ti_array_filter">
                      <option value="yes">Yes</option>
                      <option value="no">No</option>
                    </select>
                  </td>
                </tr>
                <tr class="acf-ti-relationship-builder">
                  <td class="first">
                    <label for="acf_ti_post_type">Choose Relationship:</label>
                  </td>
                  <td>
                    <?php
                    $post_types  = get_post_types( [ 'public' => true ] );
                    $excluded = [ 'attachment' ];
                    $type = !empty( $_GET ) && !empty( $_GET['acf_ti_post_type'] ) ? $_GET['acf_ti_post_type'] : 'post';
                    $args = [
                      'numberposts'=>500,
                      'post_type'=>$type
                    ];
                    ?>
                    <span>Attach to</span> <select name="acf_ti_post_type"
                            id="acf_ti_post_type"
                            onChange="acf_ti_update_post_type(event)"
                            required>
                      <?php foreach( $post_types as $type ):
                        if( !in_array( $type, $excluded ) ):
                          if( $_GET['acf_ti_post_type'] == $type ) {
                            $selected = ' selected'; } else { $selected = '';
                          } ?>
                          <option value="<?= $type; ?>" <?= $selected; ?>><?= ucfirst( $type ); ?></option>
                        <?php endif;
                      endforeach; ?>
                    </select>
                    <span>titled </span>

                    <span class="spinner is-active post-type-loader" style="float: none; display: none"></span>
                    <select name="acf_ti_post_id"
                            id="acf_ti_post_id"
                            onChange="acf_ti_update_post_type(event)"
                            required>
                      <option value=""><?php echo esc_attr(__('Select Post')); ?></option>
                      <?php
                      $pages = get_posts( $args );
                      foreach ($pages as $page) {
                        if( $_GET['acf_ti_post_id'] == $page->ID ) {
                          $selected = ' selected'; } else { $selected = '';
                        }
                        $option = '<option value="'.($page->ID).'"'.$selected.'>';
                        $option .= $page->post_title;
                        $option .= '</option>';
                        echo $option;
                      }
                      ?>
                    </select>

                    <?php if( !empty( $_GET['acf_ti_post_id'] ) ):

                      $field_objects = get_field_objects( $_GET['acf_ti_post_id'] );

                      if( !empty( $field_objects ) ): ?>
                        <span>to field group</span>
                        <select name="acf_ti_field_name" id="acf_ti_field_name">
                        <?php foreach( array_keys( $field_objects ) as $field_name ):?>
                          <option value="<?= strtolower($field_name); ?>"><?= ucfirst($field_name); ?></option>
                        <?php endforeach; ?>
                        </select>
                      <?php else: ?>
                        <input type="hidden" name="acf_ti_field_name" required />
                        <div class="notice notice-error">
                          <p><?= _e('No Advanced Custom Field Group Found!'); ?></p>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
                </tr>
                </tbody>
              </table>
              <br>
            </fieldset>
            <input type="hidden" name="page" value="/advanced-custom-fields-table-import/acf-table-import-admin" />
            <input class="button-primary" type="submit" name="Example" value="<?php esc_attr_e( 'Import CSV' ); ?>" />
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
