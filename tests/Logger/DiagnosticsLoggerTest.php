<?php
/**
 * This file is part of the Billbee API package.
 *
 * Copyright 2017 - 2020 by Billbee GmbH
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 *
 * Created by Julian Finkler <julian@mintware.de>
 */

namespace BillbeeDe\Tests\BillbeeAPI\Logger;

use BillbeeDe\BillbeeAPI\Logger\DiagnosticsLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DiagnosticsLoggerTest extends TestCase
{
    /** @var DiagnosticsLogger */
    private $logger;

    protected function setUp()
    {
        $this->logger = new DiagnosticsLogger();
    }

    protected function tearDown()
    {
        $logFile = $this->logger->getLogFile();
        if (is_file($logFile)) {
            unlink($logFile);
        }
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->logger);

        $logFile = $this->logger->getLogFile();
        $this->assertNotNull($logFile);
        $this->assertContains('billbee_api_', $logFile);
        $this->assertContains(sys_get_temp_dir(), $logFile);
    }

    public function testLogging()
    {
        $this->logger->emergency('Message 1', ['a' => 'b']);
        $this->logger->alert('Message 2', ['b' => 'c']);
        $this->logger->critical('Message 3', ['c' => 'd']);
        $this->logger->error('Message 4', ['d' => 'e']);
        $this->logger->warning('Message 5', ['e' => 'f']);
        $this->logger->notice('Message 6', ['f' => 'g']);
        $this->logger->info('Message 7', ['g' => 'h']);
        $this->logger->debug('Message 8', ['h' => 'i']);
        $this->logger->log('CUSTOM', 'Message 9', ['i' => 'j']);

        $fileContent = file_get_contents($this->logger->getLogFile());
        $emergencyPos = strpos($fileContent, 'EMERGENCY: Message: Message 1; Context: {"a":"b"}');
        $alertPos = strpos($fileContent, 'ALERT:     Message: Message 2; Context: {"b":"c"}');
        $criticalPos = strpos($fileContent, 'CRITICAL:  Message: Message 3; Context: {"c":"d"}');
        $errorPos = strpos($fileContent, 'ERROR:     Message: Message 4; Context: {"d":"e"}');
        $warningPos = strpos($fileContent, 'WARNING:   Message: Message 5; Context: {"e":"f"}');
        $noticePos = strpos($fileContent, 'NOTICE:    Message: Message 6; Context: {"f":"g"}');
        $infoPos = strpos($fileContent, 'INFO:      Message: Message 7; Context: {"g":"h"}');
        $debugPos = strpos($fileContent, 'DEBUG:     Message: Message 8; Context: {"h":"i"}');
        $customPos = strpos($fileContent, 'CUSTOM:    Message: Message 9; Context: {"i":"j"}');

        $this->assertGreaterThan(-1, $emergencyPos);
        $this->assertGreaterThan($emergencyPos, $alertPos);
        $this->assertGreaterThan($alertPos, $criticalPos);
        $this->assertGreaterThan($criticalPos, $errorPos);
        $this->assertGreaterThan($errorPos, $warningPos);
        $this->assertGreaterThan($warningPos, $noticePos);
        $this->assertGreaterThan($noticePos, $infoPos);
        $this->assertGreaterThan($infoPos, $debugPos);
        $this->assertGreaterThan($debugPos, $customPos);
    }
}