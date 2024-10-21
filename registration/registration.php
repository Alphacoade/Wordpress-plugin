<?php 
/** 
 * Registration Form
 * 
 * @package Registration Form
 * @author Alpha WebCastle
 * @copyright 2023 Alpha WebCastle 
 * @license GPL-2.0-or-later 
 * 
 * @wordpress-plugin 
 * Plugin Name: Registration Form
 * Plugin URI: https://webcastletech.com/registration-form
 * Description: Prints "Registration Form" in WordPress admin. 
 * Version: 0.0.1 
 * Author: Alpha WebCastle 
 * Author URI: https://webcastletech.com/ 
 * Text Domain: registration form
 * License: GPL v2 or later 
 * License URI: https://webcastletech.com/ */
//creating new table

function sample_plugin_activation() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'sample_form_data';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      first_name text NOT NULL,
      last_name text NOT NULL,
      email text NOT NULL,
      phone_number text NOT NULL,
      country text NOT NULL,
      password text NOT NULL,
      media text NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
register_activation_hook( __FILE__, 'sample_plugin_activation' );

function print_hello_world_title() {
  global $wpdb;
  // Enqueue the CSS file
  wp_enqueue_style('sample-css', plugins_url('assets/sample.css', __FILE__));
  wp_enqueue_style('sample-js', plugins_url('assets/sample.js', __FILE__));
  // Check if form is submitted
  if (isset($_POST['submit'])) {
    $table_name = $wpdb->prefix . 'sample_form_data';
    $wpdb->insert(
        $table_name,
        array(
            'first_name' => sanitize_text_field($_POST['firstName']),
            'last_name' => sanitize_text_field($_POST['lastname']),
            'email' => sanitize_email($_POST['email']),
            'phone_number' => sanitize_text_field($_POST['phonenumber']),
            'country' => sanitize_text_field($_POST['country']),
            'password' => sanitize_text_field($_POST['password']),
            'media' => sanitize_text_field($_POST['media']),
        )
    );
}
  // Get the value from the database
  $value = get_option('first_name_option', '');
  ?>
<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
<body>
  <h1>Registration form</h1>
  <div class="form-container">
    <form name="registerForm"  method="post">
      <label for="firstName">First Name *</label>
      <input type="text" id="firstName" name="firstName" placeholder="John"/><p class="error-message"></p>
      <label for="lastName">Last Name *</label>
      <input type="text" id="lastName" name="lastname" placeholder="Doe"/>
      <p class="error-message"></p>
      <label for="e-mail">E-mail address *</label>
      <input type="text" id="email" name="email" placeholder="john-doe@net.com"/>
      <p class="error-message"></p>
      <label for="phoneNumber">Phone Number</label>
      <input type="text" id="phoneNumber" name="phonenumber" maxlength="9" pattern=".{9,}"   title="9 characters length"placeholder="223587972"/>
      <p class="error-message"></p>
      <label for="country">Country</label>
      <input type="text" id="country" name="country" placeholder="United Kingdom"/>
      <p class="error-message"></p>
      <label for="password">Password *</label>
      <input type="password" name="password" id="password" pattern=".{8,}" title="8 characters minimum"/>
      <p class="error-message"></p>
      <p class="password-rules">Your password should contain at least 8 characters and 1 number.</p>
      <div class="radio-question">
        <p>Where did you find out about us?</p>
        <input class="radio-input" type="radio" name="media" value="TV"/> TV <br>
        <input class="radio-input" type="radio" name="media" value="radio"/> Radio <br>
        <input class="radio-input" type="radio" name="media" value="internet"/> Internet <br>
        <input class="radio-input" type="radio" name="media" value="newspaper"/> Newspaper <br>
        <input class="radio-input" type="radio" name="media" value="recommend"/> Recommendation <br>
        </div>
        <input class="button" type="submit" id="exampleButton" value="<?php _e( 'Save', 'text_domain' ); ?>" name="submit" onClick="formValidation()" />
    </form>
  </div>
</body>
  <?php
}

function display_example_value() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'sample_form_data';
  $results = $wpdb->get_results( "SELECT * FROM $table_name" );
  wp_enqueue_style('registration-form-styles', plugins_url('assets/registration-form-styles.css', __FILE__));
  $output = '<table class="regTable">';
  $output .= '<thead>
                    <tr>
                        <th>First Name</th> 
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Country</th>
                        <th>Password</th>
                        <th>Media</th>
                    </tr>
                </thead>';
  $output .= '<tbody>';
  foreach ( $results as $row ) {
      $output .= '<tr>';
      $output .= "<td>$row->first_name</td>";
      $output .= "<td>$row->last_name</td>";
      $output .= "<td>$row->email</td>";
      $output .= "<td>$row->phone_number</td>";
      $output .= "<td>$row->country</td>";
      $output .= "<td>$row->password</td>";
      $output .= "<td>$row->media</td>";
      $output .= '</tr>';
  }
  $output .= '</table>';
  return $output;
}
add_shortcode('display_value', 'display_example_value');

function hello_world_admin_menu()  {
    add_menu_page(
      'Registration Form',
      'New Registration',
      'manage_options',
      'new-registration',
      'print_hello_world_title'
    );  
    // Add the submenu
    add_submenu_page(
      'new-registration', // The slug name for the parent menu
      'Edit Registration', // The text to be displayed in the title tags of the page when the menu is selected
      'Edit Registration', // The text to be used for the menu
      'manage_options', // The capability required for this menu to be displayed to the user
      'edit-registration', // The slug name to refer to this menu by (should be unique for this menu)
      'edit_saved_data_callback' // The function to be called to output the content for this page
  );
  // Add a hidden submenu page for editing the data
  add_submenu_page(
    null, // Set the parent slug to null to hide the submenu
    'Edit Form',
    'Edit Form',
    'manage_options',
    'edit-form',
    'sample_plugin_edit_data'
  );
}  
add_action( 'admin_menu', 'hello_world_admin_menu' );
// The function that outputs the content for the submenu
function edit_saved_data_callback() {
    // Enqueue the CSS file
    wp_enqueue_style('edit-form-css', plugins_url('assets/edit-form.css', __FILE__));
    wp_enqueue_style('delete-js', plugins_url('assets/delete.js', __FILE__));
  // Here you can add the form for editing the saved data
  global $wpdb;
  $table_name = $wpdb->prefix . 'sample_form_data';
  // $results = $wpdb->get_results("SELECT * FROM $table_name");
  $counter = 1;
  ?>
    <!-- //Bootstrap CDN -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
    <!-- //Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
    <!-- fonts -->
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap");
    </style>
  <body>
    <div class="main">
      <div class="container">
        <div class="main-sub row align-items-center">
          </div>
          <div class="table-container mt-5">
            <div class="mb-2">
              <h2 class="">Your Registered Datas</h2>
              <small class="text-secondary"
                >View all registration details here.</small
              >
            </div>
            <table id="mytable" class="table align-middle mb-0 bg-white">
              <thead class="bg-light">
                <tr class="header-row">
                  <th>ID #</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email ID</th>
                  <th>Phone Number</th>
                  <th>Country</th>
                  <th>Password</th>
                  <th>Options selected</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $rows_per_page = 10;
                  // Get the current page number
                  $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
                  // Calculate the offset
                  $offset = ($paged - 1) * $rows_per_page;
                  // Get the total number of rows
                  $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                  // Calculate the total number of pages
                  $total_pages = ceil($total_rows / $rows_per_page);
                  // Get the rows for the current page
                  $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name LIMIT %d OFFSET %d", $rows_per_page, $offset));
                  // Calculate the starting point for the counter
                  $counter = ($paged - 1) * $rows_per_page + 1;
                  // Display the rows...
                  foreach($results as $row){
                ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="">
                        <p class="fw-bold mb-1"><?php echo $counter++ ?></p>
                      </div>
                    </div>
                  </td>
                  <td>
                  <span
                      ><a
                        class="btn avatar-button rounded-circle overflow-hidden p-0 m-0 d-inline-flex"
                        ><span
                          data-v-0a07f886=""
                          class="avatar-span border-0 d-inline-flex align-items-center justify-content-center text-white text-uppercase text-nowrap font-weight-normal"
                        ></span
                      ></a>
                  </span
                    ><?php echo $row->first_name ?>
                  </td>
                  <td>
                    <p class="fw-bold fw-normal mb-1"><?php echo $row->last_name ?></p>
                  </td>
                  <td>
                    <p class="fw-bold fw-normal mb-1"><?php echo $row->email ?></p>
                  </td>
                  <td><p class="fw-bold fw-normal mb-1"><?php echo $row->phone_number ?></p></td>
                  <td>
                  <p class="fw-bold fw-normal mb-1"><?php echo $row->country ?></p>
                  </td>
                  <td>
                  <p class="fw-bold fw-normal mb-1"><?php echo $row->password ?></p>
                  </td>
                  <td>
                  <p class="fw-bold fw-normal mb-1"><?php echo $row->media ?></p>
                  </td>
                  <td>
                  <button
                      type="button"
                      class="btn btn-link btn-sm btn-rounded text-primary"
                      onclick="window.location.href='admin.php?page=edit-form&id=<?php echo $row->id; ?>'"
                  >
                      <i class="me-1 action-icon bi bi-file-earmark-richtext text-primary"></i>
                      Edit
                  </button>
                    <button
                      type="button"
                      class="btn btn-link btn-sm btn-rounded text-primary btn-delete" data-id="<?php echo $row->id; ?>"
                    >
                      <i
                        class="me-1 action-icon bi bi-file-earmark-richtext text-primary"
                      ></i>
                      Delete
                    </button>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <?php
              // Generate the pagination links
              $pagination = paginate_links(array(
                  'base' => add_query_arg('paged', '%#%'),
                  'format' => '',
                  'prev_text' => __('Previous'),
                  'next_text' => __('Next'),
                  'total' => $total_pages,
                  'current' => $paged,
              ));
              // Add class to the span elements
              $pagination = str_replace('<span', '<span class="page-numbers current px-2"', $pagination);
              // Display the pagination links
              if ($pagination) {
                echo '<nav class="mt-4"><ul class="pagination justify-content-center px-2">';
                echo $pagination;
                echo '</ul></nav>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </body>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"
  ></script>
  <script>
    jQuery(document).ready(function($) {
      $('.btn-delete').click(function() {
        var id = $(this).data('id');
        $.ajax({
          url: ajaxurl, // This is a variable that WordPress automatically defines for you, it points to wp-admin/admin-ajax.php
          type: 'POST',
          data: {
            action: 'delete_row',
            id: id
          },
          success: function(response) {
            // You can refresh the page here to show the updated table
            location.reload();
          }
        });
      });
    });
  </script>
  <?php
}
function delete_row() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'sample_form_data';
  $id = intval($_POST['id']);
  $wpdb->delete($table_name, array('id' => $id));
  wp_die(); // This is required to terminate immediately and return a proper response
}
add_action('wp_ajax_delete_row', 'delete_row');
//Edit registration form
function sample_plugin_edit_data() {
  global $wpdb;
    // Enqueue the CSS file
    wp_enqueue_style('sample-css', plugins_url('assets/sample.css', __FILE__));
    wp_enqueue_style('sample-js', plugins_url('assets/sample.js', __FILE__));
  $table_name = $wpdb->prefix . 'sample_form_data';
  // Get the ID of the row to be edited
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  // Get the current data of the row
  $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
  // Display the form with the current data
  // echo '<h1>Edit Registration</h1>';
  ?>
  <body>
  <h1>Edit your form</h1>
  <div class="form-container">
    <form name="registerForm"  method="post">
      <label for="firstName">First Name *</label>
      <input type="text" id="firstName" name="firstName" placeholder="John" value="<?php echo $row->first_name ?>" required/><p class="error-message"></p>
      <label for="lastName">Last Name *</label>
      <input type="text" id="lastName" name="lastname" placeholder="Doe" value="<?php echo $row->last_name ?>" required/>
      <p class="error-message"></p>
      <label for="e-mail">E-mail address *</label>
      <input type="text" id="e-mail" name="email" placeholder="john-doe@net.com" value="<?php echo $row->email ?>" required/>
      <p class="error-message"></p>
      <label for="phoneNumber">Phone Number</label>
      <input type="text" id="phoneNumber" name="phonenumber" maxlength="9" pattern=".{9,}" value="<?php echo $row->phone_number ?>"  required title="9 characters length" placeholder="223587972"/>
      <p class="error-message"></p>
      <label for="country">Country</label>
      <input type="text" id="country" name="country" placeholder="United Kingdom" value="<?php echo $row->country ?>"/>
      <p class="error-message"></p>
      <label for="password">Password *</label>
      <input type="password" name="password" id="password" pattern=".{8,}" value="<?php echo $row->password ?>" required title="8 characters minimum"/>
      <p class="error-message"></p>
      <p class="password-rules">Your password should contain at least 8 characters and 1 number.</p>
      <div class="radio-question">
        <p>Where did you find out about us?</p>
        <input class="radio-input" type="radio" name="media" value="TV" required <?php checked( $row->media, 'TV' ); ?>/> TV <br>
        <input class="radio-input" type="radio" name="media" value="radio" required <?php checked( $row->media, 'radio' ); ?>/> Radio <br>
        <input class="radio-input" type="radio" name="media" value="internet" required <?php checked( $row->media, 'internet' ); ?>/> Internet <br>
        <input class="radio-input" type="radio" name="media" value="newspaper" required <?php checked( $row->media, 'newspaper' ); ?>/> Newspaper <br>
        <input class="radio-input" type="radio" name="media" value="recommend" required <?php checked( $row->media, 'recommend' ); ?>/> Recommendation <br>
        </div>
        <input class="button" type="submit" value="Update" name="update"/>
    </form>
  </div>
</body>
<?php
  // Update the data if the form is submitted
  if (isset($_POST['update'])) {
      $wpdb->update(
          $table_name,
          array(
              'first_name' => sanitize_text_field($_POST['firstName']),
              'last_name' => sanitize_text_field($_POST['lastname']),
              'email' => sanitize_text_field($_POST['email']),
              'phone_number' => sanitize_text_field($_POST['phonenumber']),
              'country' => sanitize_text_field($_POST['country']),
              'password' => sanitize_text_field($_POST['password']),
              'media' => sanitize_text_field($_POST['media']),
          ),
          array('id' => $id)
      );
      echo '<p>Data updated successfully.</p>';
  }
}
?>
