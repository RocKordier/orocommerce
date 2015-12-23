<?php

namespace OroB2B\Bundle\TaxBundle\Tests\Functional\Controller;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use OroB2B\Bundle\TaxBundle\Provider\BuiltInTaxProvider;

/**
 * @dbIsolation
 */
class SystemConfigurationTest extends WebTestCase
{
    /** @var ConfigManager */
    protected $configManager;

    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->configManager = $this->getContainer()->get('oro_config.global');
    }

    protected function tearDown()
    {
        $this->configManager->reset('orob2b_tax.tax_enable');
        $this->configManager->reset('orob2b_tax.tax_provider');
        $this->configManager->flush();

        parent::tearDown();
    }

    public function test()
    {
        $this->assertEmpty($this->configManager->get('orob2b_tax.tax_enable'));
        $this->assertEmpty($this->configManager->get('orob2b_tax.tax_provider'));

        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_config_configuration_system',
                ['activeGroup' => 'commerce', 'activeSubGroup' => 'tax_calculation']
            )
        );
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $token = $this->getContainer()->get('security.csrf.token_manager')->getToken('tax_calculation')->getValue();
        $form = $crawler->selectButton('Save settings')->form();
        $formData = [
            'tax_calculation' => [
                'orob2b_tax___tax_enable' => [
                    'use_parent_scope_value' => false,
                    'value' => true,
                ],
                'orob2b_tax___tax_provider' => [
                    'use_parent_scope_value' => false,
                    'value' => BuiltInTaxProvider::NAME,
                ],
                '_token' => $token,
            ],
        ];

        $this->client->followRedirects(true);
        $this->client->request($form->getMethod(), $form->getUri(), $formData);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        $this->configManager->reload();
        $this->assertTrue($this->configManager->get('orob2b_tax.tax_enable'));
        $this->assertEquals(BuiltInTaxProvider::NAME, $this->configManager->get('orob2b_tax.tax_provider'));
    }
}
