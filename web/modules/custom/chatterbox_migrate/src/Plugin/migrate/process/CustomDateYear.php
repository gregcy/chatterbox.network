<?php 
    /**
     * @file
     * Contains \Drupal\chatterbox_migrate\Plugin\migrate\process\CustomDateYear.
     */
    namespace Drupal\chatterbox_migrate\Plugin\migrate\process;
    use Drupal\migrate\ProcessPluginBase;
    use Drupal\migrate\MigrateExecutableInterface;
    use Drupal\migrate\Row;
    /**
     * This plugin converts Drupal 6 Date fields to Drupal 8.
     *
     * @MigrateProcessPlugin(
     *   id = "custom_date_year"
     * )
     */
    class CustomDateYear extends ProcessPluginBase {
        /**
         * {@inheritdoc}
         */
        public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
            $value_temp = $value['value'];
            $value_return = substr($value_temp,0,4);
            return $value_return .'-01-01';
        }
    }
