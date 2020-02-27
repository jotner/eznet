<?php
class Shipping extends WC_Shipping_Method
{
    /**
     * The ID of the shipping method.
     *
     * @var string
     */
    public $id = 'custom_shipping';

    /**
     * The title of the method.
     *
     * @var string
     */
    public $method_title = 'Custom Shipping';

    /**
     * The description of the method.
     *
     * @var string
     */
    public $method_description = 'Custom Shipping';

    /**
     * The supported features.
     *
     * @var array
     */
    public $supports = [
        'settings',
    ];

    /**
     * Initialize a new shipping method instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->init_form_fields();
        $this->init_settings();
        $this->registerHooks();

        $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'no';
        $this->title = isset($this->settings['title']) ? $this->settings['title'] : 'Custom Shipping';
    }

    /**
     * Initialize the form fields.
     *
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Enable'),
                'type' => 'checkbox',
                'description' => __('Enable this shipping method.'),
                'default' => 'yes',
            ],
           'title' => [
              'title' => __('Title'),
                'type' => 'text',
                'description' => __('Title to be display on site.'),
                'default' => __('Custom Shipping'),
            ],
        ];
    }

    /**
     * Calculate the shipping fees.
     *
     * @param  array  $package
     * @return void
     */
    public function calculate_shipping($package = [])
    {
        $ids = [];

        foreach ($package['contents'] as $id => $product) {
            $ids[] = $product['product_id'];
        }

        $this->add_rate([
            'id' => $this->id,
            'label' => $this->title,
            'cost' => in_array(12, $ids) ? 0 : 100,
        ]);
    }

    /**
     * Register the shipping method hooks.
     *
     * @return void
     */
    public function registerHooks()
    {
        add_action("woocommerce_update_options_shipping_{$this->id}", [$this, 'process_admin_options']);
    }
}