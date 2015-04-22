<?php

namespace OroB2B\Bundle\RFPAdminBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use OroB2B\Bundle\RFPAdminBundle\Tests\Functional\DataFixtures\LoadRequestStatusData;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class RequestStatusControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());

        $this->loadFixtures(['OroB2B\Bundle\RFPAdminBundle\Tests\Functional\DataFixtures\LoadRequestStatusData']);
    }

    public function testDeleteAndRestoreAction()
    {
        $entityManager = $this->getContainer()
            ->get('doctrine')
            ->getManagerForClass('OroB2BRFPAdminBundle:RequestStatus');

        $entityRepository = $entityManager->getRepository('OroB2BRFPAdminBundle:RequestStatus');

        $requestStatus = $entityRepository->findOneBy(['name' => LoadRequestStatusData::NAME_NOT_DELETED]);
        $this->assertFalse($requestStatus->getDeleted());

        $this->client->request(
            'DELETE',
            $this->getUrl('orob2b_api_rfp_delete_request_status', ['id' => $requestStatus->getId()])
        );
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);

        $entityManager->clear();

        $requestStatus = $entityRepository->findOneBy(['name' => LoadRequestStatusData::NAME_NOT_DELETED]);
        $this->assertTrue($requestStatus->getDeleted());

        $this->client->request(
            'GET',
            $this->getUrl('orob2b_api_rfp_restore_request_status', ['id' => $requestStatus->getId()])
        );
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);

        $entityManager->clear();

        $requestStatus = $entityRepository->findOneBy(['name' => LoadRequestStatusData::NAME_NOT_DELETED]);
        $this->assertFalse($requestStatus->getDeleted());
    }
}
