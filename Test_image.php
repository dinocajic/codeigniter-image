<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Test_image
 *
 * config/autoload.php :: $autoload['helper'] = array('form');
 * images table:
 *
 * CREATE TABLE `images` (
 * `id` int(11) NOT NULL,
 * `img_location` varchar(1000) NOT NULL
 * ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 */

class Test_image extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model("image_model");
    }

    public function index() {
        //$this->upload_form();
        //$this->get_image_by_id();
        //$this->get_image_id();
        //$this->add_image();
        //$this->multiple_image_upload_form();
        //$this->delete_image_from_table();
        $this->delete_image_from_folder();
    }

    public function upload_form() {
        echo form_open_multipart('test/test_image/upload_resize_image');
        ?>
        <input type="file" name="this_image" size="20" />

        <br /><br />

        <input type="submit" value="upload" />

        </form>
        <?php
    }

    /**
     * Gets called from upload_form()
     *
     * Attempts to upload the image to the assets/images/upload folder.
     * If it's not successful, an error key appears and the error is displayed.
     * If it is successfully uploaded, we grab the filename and resize the image with the specified width and height
     * and store it in the same folder as the uploaded image.
     *
     * $upload_image_success['upload_data']['full_path'] = D:/software/assets/images/upload/567_silver7.png
     */
    public function upload_resize_image() {
        $upload_image_success = $this->image_model->upload_image('this_image', './assets/images/upload');

        if ( array_key_exists("error", $upload_image_success ) ) {
            echo $upload_image_success['error'];

        } else {
            $source_file = explode("assets", $upload_image_success['upload_data']['full_path']);
            $source_file = "assets" . $source_file[1];

            $width         = 75;
            $height        = 50;

            $resize_success = $this->image_model->resize_image($source_file, $width, $height);
            var_dump($upload_image_success);
            var_dump($resize_success);
        }
    }

    public function get_image_by_id() {
        $id = 300;

        var_dump(
            $this->image_model->get_image_by_id($id)
        );
    }

    public function get_image_id() {
        $img_location = 'img/models/222.jpg';

        var_dump(
            $this->image_model->get_image_id($img_location)
        );
    }

    /**
     * Can be combined with upload_resize_image()
     */
    public function add_image() {
        $img_location = 'img/models/223.jpg';

        var_dump(
            $this->image_model->add_image($img_location)
        );
    }

    public function multiple_image_upload_form() {
        echo form_open_multipart('test/test_image/upload_multiple_images');
        ?>
        <input type="file" name="these_images[]" multiple="multiple">

        <br /><br />

        <input type="submit" value="upload" />

        </form>
        <?php
    }

    public function upload_multiple_images() {
        $this->load->library('upload');

        $data_info = array();
        $files     = $_FILES;
        $num_files = count( $_FILES['these_images']['name'] );

        for($i = 0; $i < $num_files; $i++) {
            $_FILES['these_images']['name']     = $files['these_images']['name'][$i];
            $_FILES['these_images']['type']     = $files['these_images']['type'][$i];
            $_FILES['these_images']['tmp_name'] = $files['these_images']['tmp_name'][$i];
            $_FILES['these_images']['error']    = $files['these_images']['error'][$i];
            $_FILES['these_images']['size']     = $files['these_images']['size'][$i];

            // image upload options
            $config = array();
            $config['upload_path']   = './assets/images/upload/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']      = 2048; //in kilobytes = 2MB

            $this->upload->initialize($config);
            $this->upload->do_upload('these_images');
            $data_info[] = $this->upload->data();
        }

        var_dump($data_info);
    }

    public function delete_image_from_table() {
        $id = 10;

        var_dump(
            $this->image_model->delete_image_from_table($id)
        );
    }

    public function delete_image_from_folder() {
        $image = "./assets/images/upload/119_chrome1.png";

        var_dump(
            $this->image_model->delete_image_from_folder($image)
        );
    }
}