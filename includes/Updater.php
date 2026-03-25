<?php

class EL_Updater {

    private $file;
    private $plugin;
    private $basename;
    private $username = 'YOUR_GITHUB_USERNAME';
    private $repo = 'elementlayer';

    public function __construct($file) {
        $this->file = $file;
        $this->basename = plugin_basename($file);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    public function check_update($transient) {

        if (empty($transient->checked)) return $transient;

        $remote = wp_remote_get(
            "https://api.github.com/repos/{$this->username}/{$this->repo}/releases/latest"
        );

        if (is_wp_error($remote)) return $transient;

        $data = json_decode(wp_remote_retrieve_body($remote));

        if (!$data || empty($data->tag_name)) return $transient;

        $current_version = get_plugin_data($this->file)['Version'];
        $remote_version = ltrim($data->tag_name, 'v');

        if (version_compare($remote_version, $current_version, '>')) {

            $plugin = new stdClass();
            $plugin->slug = 'elementlayer';
            $plugin->plugin = $this->basename;
            $plugin->new_version = $remote_version;
            $plugin->url = $data->html_url;
            $plugin->package = $data->zipball_url;

            $transient->response[$this->basename] = $plugin;
        }

        return $transient;
    }

    public function plugin_info($res, $action, $args) {

        if ($action !== 'plugin_information') return $res;
        if ($args->slug !== 'elementlayer') return $res;

        $remote = wp_remote_get(
            "https://api.github.com/repos/{$this->username}/{$this->repo}/releases/latest"
        );

        if (is_wp_error($remote)) return $res;

        $data = json_decode(wp_remote_retrieve_body($remote));

        $res = new stdClass();
        $res->name = 'ElementLayer';
        $res->slug = 'elementlayer';
        $res->version = ltrim($data->tag_name, 'v');
        $res->download_link = $data->zipball_url;
        $res->sections = [
            'description' => $data->body
        ];

        return $res;
    }
}