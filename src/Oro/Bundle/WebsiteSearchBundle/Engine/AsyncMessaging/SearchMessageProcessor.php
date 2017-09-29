<?php

namespace Oro\Bundle\WebsiteSearchBundle\Engine\AsyncMessaging;

use Oro\Bundle\WebsiteSearchBundle\Engine\AsyncIndexer;
use Oro\Bundle\SearchBundle\Engine\IndexerInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Client\Config as MessageQueConfig;
use Oro\Component\MessageQueue\Util\JSON;

class SearchMessageProcessor implements MessageProcessorInterface
{
    /**
     * @var IndexerInterface $indexer
     */
    private $indexer;

    /**
     * @param IndexerInterface $indexer
     */
    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $topic = $message->getProperty(MessageQueConfig::PARAMETER_TOPIC_NAME);
        $data = JSON::decode($message->getBody());

        return $this->executeIndexActionByTopic($topic, $data);
    }

    /**
     * @param string $topic
     * @param mixed  $data
     *
     * @return string
     */
    protected function executeIndexActionByTopic(string $topic, $data)
    {
        switch ($topic) {
            case AsyncIndexer::TOPIC_SAVE:
                $this->indexer->save($data['entity'], $data['context']);

                $result = static::ACK;
                break;

            case AsyncIndexer::TOPIC_DELETE:
                $this->indexer->delete($data['entity'], $data['context']);

                $result = static::ACK;
                break;

            case AsyncIndexer::TOPIC_REINDEX:
                $this->indexer->reindex($data['class'], $data['context']);

                $result = static::ACK;
                break;

            case AsyncIndexer::TOPIC_RESET_INDEX:
                $this->indexer->resetIndex($data['class'], $data['context']);

                $result = static::ACK;
                break;

            default:
                $result = static::REJECT;
        }

        return $result;
    }
}
