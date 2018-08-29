<?php
/**
 * Image Upload Class
 *
 * @author Dino Cajic
 * @email dinocajic@gmail.com
 */
class Image_model extends CI_Model {

    /**
     * @param string $file   - the name of the input, for example <input type="file" name="this_image" size="20" />
     * @param string $folder - for example, ./assets/images/upload
     *
     * @return array
     */
    public function upload_image($file, $folder) {
        $config = array();
        $config['upload_path']   = $folder;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']      = 2048; //in kilobytes = 2MB

        $this->load->library('upload', $config);

        if ( !$this->upload->do_upload($file) ) {
            $error = array('error' => $this->upload->display_errors());

            return $error;
        }

        $data = array('upload_data' => $this->upload->data());

        return $data;
    }

    /**
     * Tells the resize_image method to look for an image called $source_image located in the source_image folder,
     * then create a thumbnail that is $width X $height pixels using the GD2 image_library. Since the maintain_ratio
     * option is enabled, the thumb will be as close to the target width and height as possible while preserving the
     * original aspect ratio. The thumbnail will be called xxx_thumb.jpg and located at the same level as source_image.
     *
     * @param string $source_image - must be relative to root directory i.e. assets/images/logo.png
     * @param int $width
     * @param int $height
     *
     * @return bool
     */
    public function resize_image($source_image, $width, $height) {
        $config['image_library']  = 'gd2';
        $config['source_image']   = $source_image;
        $config['create_thumb']   = true;
        $config['maintain_ratio'] = true;
        $config['width']          = $width;
        $config['height']         = $height;

        $this->load->library('image_lib', $config);

        $this->image_lib->resize();

        if ( !$this->image_lib->resize() ) {
            echo $this->image_lib->display_errors();
            return false;
        }

        return true;
    }

    /**
     * Returns the image location from the images table by specifying the id
     *
     * @param int $img_id
     *
     * @return bool|string
     */
    public function get_image_by_id($img_id) {
        $this->db->select("img_location");
        $this->db->where("id", $img_id);
        $this->db->from("images");

        $query = $this->db->get();
        $row   = $query->row();

        if ( is_object($row) ) {
            return $row->img_location;
        }

        return false;
    }

    /**
     * Gets the id for a specific image from the images table
     *
     * @param string $img_location
     *
     * @return int
     */
    public function get_image_id($img_location) {
        $this->db->select("id");
        $this->db->where("img_location", $img_location);
        $this->db->from("images");

        $query = $this->db->get();
        $row   = $query->row();

        if ( is_object($row) ) {
            return $row->id;
        }

        return -1;
    }

    /**
     * Inserts image into database
     *
     * @param $img_location
     *
     * @return int
     */
    public function add_image($img_location) {
        $data = array(
            'img_location' => $img_location
        );

        $this->db->insert('images', $data);

        if ( $this->db->affected_rows() > 0 ) {
            return $this->db->insert_id();
        } else {
            return -1;
        }
    }

    /**
     * Deletes an image from the images table
     *
     * @param $img_id
     *
     * @return mixed
     */
    public function delete_image_from_table($img_id) {
        $this->db->where('id', $img_id);
        $this->db->delete('images');

        return $this->db->affected_rows();
    }

    /**
     * Deletes a file from the specified path
     *
     * @param string $file - Relative path, for example ./assets/images/uploads/123.jpg
     *
     * @return bool
     */
    public function delete_image_from_folder($file) {
        if ( file_exists($file) ) {
            return unlink( $file );
        }

        return false;
    }
}