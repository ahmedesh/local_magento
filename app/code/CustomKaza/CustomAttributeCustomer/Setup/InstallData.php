<?php

/**
 * @package   Thecoachsmb\CustomerAttribute
 */

namespace CustomKaza\CustomAttributeCustomer\Setup;

use CustomKaza\CustomAttributeCustomer\Model\Config\Source\Gender;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        $customerEntity = $this->eavConfig->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'mobile_number',
            [
                'type' => 'varchar',
                'label' => 'mobile_number',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'unique' => true,
                'user_defined' => true,
                'sort_order' => 1000, // position && sort_order must take the same value
                'position' => 1000,   // position && sort_order must take the same value
                'system' => 0,
            ]);
        // ================ add Acumatica_Customer_id ===============
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'Acumatica_Customer_id',
            [
                'type' => 'varchar',
                'label' => 'Acumatica_Customer_id',
                'input' => 'text',
                'visible' => true,
                'unique' => true,
                'required' => false,
                'user_defined' => true,
                'sort_order' => 1001,
                'position' => 1001,
                'system' => 0,
            ]);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            'Gender',
            [
                'type' => 'int',
                'label' => 'Gender',
                'input' => 'select',
//              'source' => 'CustomKaza\CustomAttributeSelect\Model\Config\Source\Gender', // must declare source when choice input with => select
                'source' => Gender::class, // must declare source when choice input with => select
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'position' => 50, // position && sort_order must take the same value
                'sort_order' => 50, // position && sort_order must take the same value
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                //'group' => 'Related Content',
            ]);
        $customAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'mobile_number');
        $customAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer']
        ]);
        $customAttribute->save();

        $customAttributeacu = $this->eavConfig->getAttribute(Customer::ENTITY, 'Acumatica_Customer_id');
        $customAttributeacu->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer']
        ]);
        $customAttributeacu->save();

        $customAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'Gender');
        $customAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer']
        ]);
        $customAttribute->save();
    }
}
