<?php declare(strict_types=1);

namespace CustomKaza\CustomAttributeSelect\Model\Config\Source;


use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

Class Gender extends AbstractSource implements OptionSourceInterface
{
    public function getAllOptions()
    {
        // TODO: Implement getAllOptions() method.
        return[
            [
              'value' => 1,
              'label' => 'Male'
            ],
            [
                'value' => 2,
                'label' => 'FeMale'
                ]
        ];
    }
}
