<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

class Ps_metrics extends Module
{
    /** @var bool */
    public $bootstrap;

    /** @var string */
    public $confirmUninstall;

    /** @var string */
    public $idPsAccounts;

    /** @var string */
    public $idPsMetrics;

    /** @var string */
    public $template_dir;

    /** @var string */
    public $emailSupport;

    /** @var string */
    public $termsOfServiceUrl;

    public function __construct()
    {
        $this->name = 'ps_metrics';
        $this->tab = 'advertising_marketing';
        $this->version = '3.4.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->module_key = '697657ffe038d20741105e95a10b12d1';
        $this->bootstrap = false;
        $this->idPsAccounts = '49648';
        $this->idPsMetrics = '49583';
        $this->emailSupport = 'support-ps-metrics@prestashop.com';
        $this->termsOfServiceUrl =
            'https://www.prestashop.com/en/prestashop-account-privacy';

        parent::__construct();

        $this->displayName = $this->l('PrestaShop Metrics');
        $this->description = $this->l(
            'Optimize your business with a data-driven approach by gaining a complete view of your business in real time.'
        );
        $this->confirmUninstall = $this->l(
            'Are you sure you want to uninstall this module?'
        );
        $this->ps_versions_compliancy = [
            'min' => '1.7.5',
            'max' => _PS_VERSION_,
        ];
        $this->template_dir =
            '../../../../modules/' . $this->name . '/views/templates/admin/';
    }

    /**
     * This method is trigger at the installation of the module
     *
     * @return bool
     */
    public function install()
    {
        parent::install();

        $legacyModuleInstaller = new PrestaShop\Module\Ps_metrics\LegacyModuleInstaller(
            $this
        );
        $installModule = $legacyModuleInstaller->legacyModuleInstaller();
        $nativeStats = $legacyModuleInstaller->legacyNativeStatsHandler();

        return $this->registerHook('displayAdminAfterHeader') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('dashboardZoneTwo') &&
            $installModule->updateModuleHookPosition('dashboardZoneTwo', 0) &&
            $installModule->setConfigurationValues() &&
            $nativeStats->install();
    }

    /**
     * Triggered at the uninstall of the module
     *
     * @return bool
     */
    public function uninstall()
    {
        /** @var PrestaShop\Module\Ps_metrics\Module\Uninstall $uninstallModule */
        $uninstallModule = $this->getService('ps_metrics.module.uninstall');

        /** @var PrestaShop\Module\Ps_metrics\Tracker\Segment $segment */
        $segment = $this->getService('ps_metrics.tracker.segment');
        if (!empty($segment)) {
            $segment->setMessage('[MTR] Uninstall Module');
            $segment->track();
        }

        /** @var PrestaShop\Module\Ps_metrics\Handler\NativeStatsHandler $nativeStats */
        $nativeStats = $this->getService('ps_metrics.handler.native.stats');

        return $uninstallModule->resetConfigurationValues() &&
            $uninstallModule->unsubscribePsEssentials() &&
            $nativeStats->uninstall() &&
            parent::uninstall();
    }

    /**
     * Activate current module.
     *
     * @param bool $force_all If true, enable module for all shop
     *
     * @return bool
     */
    public function enable($force_all = false)
    {
        parent::enable($force_all);

        $legacyModuleInstaller = new PrestaShop\Module\Ps_metrics\LegacyModuleInstaller(
            $this
        );
        $segment = $legacyModuleInstaller->legacyModuleInstallerSegment();
        if (!empty($segment)) {
            $segment->setMessage('[MTR] Enable Module');
            $segment->track();
        }

        return true;
    }

    /**
     * Desactivate current module.
     *
     * @param bool $force_all If true, disable module for all shop
     *
     * @return bool
     */
    public function disable($force_all = false)
    {
        parent::disable($force_all);

        /** @var PrestaShop\Module\Ps_metrics\Tracker\Segment $segment */
        $segment = $this->getService('ps_metrics.tracker.segment');
        if (!empty($segment)) {
            $segment->setMessage('[MTR] Disable Module');
            $segment->track();
        }

        return true;
    }

    /**
     * hookDashboardZoneTwo
     *
     * @return string
     */
    public function hookDashboardZoneTwo()
    {
        $assets = $this->loadAssets();
        Media::addJsDef($assets);

        return $this->display(
            __FILE__,
            '/views/templates/hook/HookDashboardZoneTwo.tpl'
        );
    }

    /**
     * Load the configuration form.
     *
     * @return string
     * @return void
     */
    public function getContent()
    {
        $link = $this->context->link;
        \Tools::redirectAdmin(
            $link->getAdminLink('MetricsController', true, [
                'route' => 'metrics_page',
            ]) . '#/settings'
        );
    }

    /**
     * hook Backoffice top pages
     *
     * @return string|false
     */
    public function hookDisplayAdminAfterHeader()
    {
        /** @var PrestaShop\Module\Ps_metrics\Helper\PrestaShopHelper $prestashopHelper */
        $prestashopHelper = $this->getService('ps_metrics.helper.prestashop');

        if (!Module::isEnabled($this->name)) {
            return false;
        }
        if ($prestashopHelper->getControllerName() === 'AdminStats') {
            return $this->display(
                __FILE__,
                '/views/templates/hook/HookDashboardZoneTwo.tpl'
            );
        }
        if ($prestashopHelper->getControllerName() === 'AdminOrders') {
            $assets = $this->loadAssets('AdminOrders', false);

            $twig = $this->loadInstance('twig');

            return $twig->render(
                '@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig',
                $assets
            );
        }

        return false;
    }

    /**
     * Load an instance of the service
     *
     * @param string $instance_name
     *
     * @return mixed
     */
    private function loadInstance($instance_name)
    {
        /** @var Symfony\Component\DependencyInjection\ContainerInterface $instance */
        $instance = PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();

        return $instance->get($instance_name);
    }

    /**
     * Hook set media for old stats pages for loading dashboard box
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia()
    {
        /** @var PrestaShop\Module\Ps_metrics\Helper\PrestaShopHelper $prestashopHelper */
        $prestashopHelper = $this->getService('ps_metrics.helper.prestashop');

        if (!Module::isEnabled($this->name)) {
            return;
        }
        if ($prestashopHelper->getControllerName() === 'AdminStats') {
            $assets = $this->loadAssets('AdminStats');
            Media::addJsDef($assets);
        }

        if ($prestashopHelper->getControllerName() === 'AdminOrders') {
            $assets = $this->loadAssets('AdminOrders', false);
        }
    }

    /**
     * Load VueJs App Dashboard and set JS variable for Vue
     *
     * @param string $currentPage
     * @param bool $legacy
     *
     * @return array
     */
    private function loadAssets($currentPage = 'dashboard', $legacy = true)
    {
        $assets = $this->buildAssets($currentPage);

        if ($legacy) {
            $this->context->smarty->assign(
                'pathMetricsApp',
                $assets['pathMetricsApp']
            );
            $this->context->smarty->assign(
                'pathMetricsAssets',
                $assets['pathMetricsAssets']
            );
            $this->context->smarty->assign(
                'pathMetricsAppSourceMap',
                $assets['pathMetricsAppSourceMap']
            );
        } else {
            $twig = $this->loadInstance('twig');

            $twig->addGlobal('pathMetricsApp', $assets['pathMetricsApp']);
            $twig->addGlobal('pathMetricsAssets', $assets['pathMetricsAssets']);
            $twig->addGlobal(
                'pathMetricsAppSourceMap',
                $assets['pathMetricsAppSourceMap']
            );
            $twig->addGlobal(
                'nativeStatsModulesEnabled',
                $assets['nativeStatsModulesEnabled']
            );
        }

        return $assets;
    }

    /**
     * Build assets to set them in smarty or Twig
     *
     * @param string $currentPage
     *
     * @return array
     */
    private function buildAssets($currentPage = 'dashboard')
    {
        /** @var PrestaShop\Module\Ps_metrics\Helper\ConfigHelper $configHelper */
        $configHelper = $this->getService('ps_metrics.helper.config');

        /** @var PrestaShop\Module\Ps_metrics\Helper\PrestaShopHelper $prestashopHelper */
        $prestashopHelper = $this->getService('ps_metrics.helper.prestashop');

        /** @var PrestaShop\Module\Ps_metrics\Helper\ConfigHelper $configHelper */
        $configHelper = $this->getService('ps_metrics.helper.config');

        /** @var PrestaShop\Module\Ps_metrics\Helper\ToolsHelper $toolsHelper */
        $toolsHelper = $this->getService('ps_metrics.helper.tools');

        /** @var PrestaShop\Module\Ps_metrics\Handler\NativeStatsHandler $nativeStats */
        $nativeStats = $this->getService('ps_metrics.handler.native.stats');

        /** @var PrestaShop\Module\Ps_metrics\Helper\ModuleHelper $moduleHelper */
        $moduleHelper = $this->getService('ps_metrics.helper.module');

        $pathMetricsApp = $configHelper->getUseLocalVueApp()
            ? $this->_path . '_dev/dist/js/metrics.js'
            : $configHelper->getPsMetricsCdnUrl() . 'js/metrics.js';
        $pathMetricsAssets = $configHelper->getUseLocalVueApp()
            ? $this->_path . '_dev/dist/css/style.css'
            : $configHelper->getPsMetricsCdnUrl() . 'css/style.css';

        $pathMetricsAppSourceMap = null;
        if (
            file_exists(
                _PS_MODULE_DIR_ . $this->name . '/_dev/dist/js/metrics.js.map'
            )
        ) {
            $pathMetricsAppSourceMap = $configHelper->getUseLocalVueApp()
                ? $this->_path . '_dev/dist/js/metrics.js.map'
                : $configHelper->getPsMetricsCdnUrl() . 'js/metrics.js.map';
        }

        $link = $this->context->link;

        return [
            'pathMetricsApp' => $pathMetricsApp,
            'pathMetricsAppSourceMap' => $pathMetricsAppSourceMap,
            'pathMetricsAssets' => $pathMetricsAssets,
            'contextPsAccounts' => $this->loadPsAccountsAssets(),
            'oAuthGoogleErrorMessage' => $toolsHelper->getValue(
                'google_message_error'
            ),
            'metricsApiUrl' => $prestashopHelper->getLinkWithoutToken(
                'MetricsResolverController',
                'metrics_api_resolver'
            ),
            'adminToken' => $prestashopHelper->getTokenFromAdminLink(
                'MetricsResolverController',
                'metrics_api_resolver'
            ),
            'metricsModule' => $moduleHelper->buildModuleInformations(
                'ps_metrics'
            ),
            'eventBusModule' => $moduleHelper->buildModuleInformations(
                'ps_eventbus'
            ),
            'accountsModule' => $moduleHelper->buildModuleInformations(
                'ps_accounts'
            ),
            'graphqlEndpoint' => $link->getAdminLink(
                'MetricsGraphqlController',
                true,
                ['route' => 'metrics_graphql']
            ),
            'isoCode' => $prestashopHelper->getLanguageIsoCode(),
            'currencyIsoCode' => $prestashopHelper->getCurrencyIsoCode(),
            'currentPage' => $currentPage,
            'fullscreen' => false,
            'nativeStatsModulesEnabled' => $nativeStats->nativeStatsIsEnabled(),
        ];
    }

    /**
     * Retrieve service
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        return $this->get($serviceName);
    }

    /**
     * See https://github.com/PrestaShopCorp/prestashop-accounts-installer
     *
     * @return array
     */
    public function loadPsAccountsAssets()
    {
        if (\Module::isInstalled('ps_accounts')) {
            /** @var PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts $accounts */
            $accounts = $this->get('ps_accounts.facade');
            $psAccounts = $accounts->getPsAccountsService();

            $this->context->smarty->assign(
                'urlAccountsVueCdn',
                $psAccounts->getAccountsVueCdn()
            );

            return $accounts->getPsAccountsPresenter()->present($this->name);
        }

        return [];
    }
}
