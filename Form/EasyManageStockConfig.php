<?php

/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace EasyManageStock\Form;

use EasyManageStock\EasyManageStock;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Form\BaseForm;


/**
 * Class EasyManageStockConfig
 * @package EasyManageStock\Form
 */
class EasyManageStockConfig extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'stock',
                IntegerType::class,
                [
                    'label' => $this->translator->trans('Stock', [], 'easymanagestock'),
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan(['value' => 0])
                    ]
                ]
            );
    }

    public static function getName()
    {
        return 'easymanagestock_configuration_form';
    }
}
