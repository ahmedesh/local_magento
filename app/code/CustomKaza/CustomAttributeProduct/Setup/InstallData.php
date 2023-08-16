<?php

/**
 * @package   Thecoachsmb\CustomerAttribute
 */

namespace CustomKaza\CustomAttributeProduct\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory
    ){
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(Product::ENTITY, 'dc');
        // ================ add dc attribute  ===============
        $eavSetup->addAttribute(
            Product::ENTITY,
            'dc',
            [
                'type' => 'varchar',
                'label' => 'dc',
                'input' => 'text',
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'position' => 50, // position && sort_order must take the same value
                'sort_order' => 50, // position && sort_order must take the same value
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                //'group' => 'Related Content',
            ]);

//        $productAttribute = $this->eavConfig->getAttribute(Product::ENTITY, 'dc');
//        $productAttribute->save();
    }
}
