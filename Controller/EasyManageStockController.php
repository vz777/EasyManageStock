<?php

namespace EasyManageStock\Controller;

use EasyManageStock\EasyManageStock;
use EasyManageStock\Form\EasyManageStockConfig;
use Propel\Generator\Model\Database;
use Propel\Runtime\Propel;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Exception\InvalidArgumentException;
use Thelia\Exception\TheliaProcessException;
use Thelia\Model\ProductQuery;
use Thelia\Model\Product;
use Thelia\Model\ProductSaleElements;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Log\Tlog;
use Thelia\Tools\URL;

class EasyManageStockController extends BaseAdminController
{
    /**
     * Update the stock level of a product.
     *
     * @param Request $request
     *
     * @throws InvalidArgumentException
     */
    public function updateStockAction(Request $request)
    {
        
        // Check the user has the correct permissions to access this feature
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'EasyManageStock', AccessManager::UPDATE)) {
            return $response;
        }
        // Get the product ID and PSE ID from the request
        $productId = $request->get('product_id');
        $pseId = $request->get('pse_id');

        // Get the product from the database
        $product = ProductQuery::create()->findPk($productId);

        // Check the product exists
        if (null === $product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d does not exist', $productId));
        }

        // Get the product sale element from the database
        $pse = ProductSaleElementsQuery::create()
        ->filterByProductId($productId)
        ->findPk($pseId);

        // Check the PSE exists
        if (null === $pse) {
            throw new InvalidArgumentException(sprintf('Product sale element with ID %d does not exist', $pseId));
        }
        
        $con = Propel::getConnection(); 
        $con->beginTransaction();
        
        // Get the form
        $form = $this->createForm(EasyManageStockConfig::getName());

        // Handle the form submission
        try {
            $data = $this->validateForm($form, 'POST');

            // Update the PSE's stock level
            $pse->setQuantity($data['stock']->getData());
            $pse->save();
            
            //log for debug will delete this on production
            Tlog::getInstance()->debug("Product sale element %d stock level have been updated successfully from easymanagestock.");

            $con->commit();
            Tlog::getInstance()->debug("Product sale element push to database from easymanagestock.");

            return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/easymanagestock'));            

            Tlog::getInstance()->debug("Easymanagestock success redirect");

        } catch (TheliaProcessException $e) {
            // Log the form validation errors
            $this->getContainer()->get('thelia.logger')->logError(
                EasyManageStock::MESSAGE_DOMAIN,
                $e->getMessage()
            );
        }
    }
}