<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_User_Role_Customizer
 * @subpackage Wp_User_Role_Customizer/admin/partials
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()) ?></h1>
    <?php
    if (isset($_GET['resp'])) {

        $resp = $_GET['resp'];
        if ($resp == "success") {
            ?>
            <h3 class="text-success"><?php _e("Succesfully added", "wurc") ?></h3>
            <?php
        } else if ($resp == "roleexicst") {
            ?>
            <h3 class="text-danger"><?php _e("Role already exists", "wurc") ?></h3>
            <?php
        }
    }
    global $submenu, $menu;

    $roles = $this->get_editable_roles();

    ?>

    <form name="add-role" id="add_role" class="list-of-active-plugins"
          action="<?php echo admin_url('admin-post.php'); ?>" method=post>
        <label class="" for="rolename"><?php _e("New role name:", "wurc") ?> </label>
        <input class="role-name-input" type="text" name="rolename">
        <label for="preroles"><?php _e("Inherit permissions from:", "wurc") ?> </label>
        <select name="preroles">
            <option value="wurc-no-role-inherited"><?php _e("No role", "wurc") ?> </option>
            <?php foreach ($roles as $role_slug => $role) {
                ?>
                <option value="<?php _e($role_slug) ?>"><?php _e($role['name']) ?></option>
            <?php } ?>

        </select>
        <div class="row">

            <?php foreach ($menu

                           as $key => $value) {

                if ($value[0] === '') {
                    continue;
                } ?>

                <div class="menu-checkboxes col-md-3 card dropdown">

                    <?php if (isset($submenu[$value[2]])) { ?>

                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php _e($this->remove_numbers($value[0])) ?>
                        </button>
                        <?php
                    } else {
                        ?>
                        <input type="checkbox" onClick="handleChange(this,'<?php _e($value[1]) ?>' );" name="slugs[]"
                               value="<?php _e($value[2]) ?>"/>
                        <label class="single-active-plugin-label"><?php _e($this->remove_numbers($value[0])) ?> </label>

                    <?php } ?>


                    <div class="dropdown-menu">
                        <?php
                        if (isset($submenu[$value[2]])) {
                            foreach ($submenu[$value[2]] as $sm) {
                                ?>
                                <div class="sub-menu-checkboxes dropdown-item">

                                    <input type="checkbox" onClick="handleChange(this, '<?php _e($sm[1]) ?>');"
                                           name="slugs[]"
                                           value="<?php _e($sm[2]) ?>"/>

                                    <label class="single-active-plugin-label"><?php _e($this->remove_numbers($sm[0])) ?> </label>
                                </div>
                            <?php }
                        }
                        ?>
                    </div>
                </div>
            <?php }
            ?>
        </div>
        <div id="hiddeninput" style="display:none;">
        </div>
        <input type="hidden" name="action" value="wurc_add_role">
        <button class="btn btn-primary wurc-submit-button" type="submit"
                name="add-new-role-submit"><?php _e("Add Role", "wurc") ?></button>
    </form>
</div>

