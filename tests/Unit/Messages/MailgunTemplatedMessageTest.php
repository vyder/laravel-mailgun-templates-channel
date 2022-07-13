<?php

/**
 * This file is part of laravel-mailgun-templated-messages, a Matchory application.
 *
 * Unauthorized copying of this file, via any medium, is strictly prohibited.
 * Its contents are strictly confidential and proprietary.
 *
 * @copyright 2020–2022 Matchory GmbH · All rights reserved
 * @author    Moritz Friedrich <moritz@matchory.com>
 */

declare(strict_types=1);

namespace Matchory\MailgunTemplatedMessages\Tests\Messages;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use JsonException;
use Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage;
use Matchory\MailgunTemplatedMessages\Tests\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class MailgunTemplatedMessageTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setBlindCarbonCopy
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getBlindCarbonCopy
     */
    public function testBccSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getBlindCarbonCopy());
        $message->bcc('foo');
        self::assertSame('foo', $message->getBlindCarbonCopy());
        $message->setBlindCarbonCopy('bar');
        self::assertSame('bar', $message->getBlindCarbonCopy());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setCarbonCopy
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getCarbonCopy
     */
    public function testCcSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getCarbonCopy());
        $message->cc('foo');
        self::assertSame('foo', $message->getCarbonCopy());
        $message->setCarbonCopy('bar');
        self::assertSame('bar', $message->getCarbonCopy());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArray(): void
    {
        $message = new MailgunTemplatedMessage('foo');

        self::assertEquals([
            'template' => 'foo',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingBcc(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->bcc('foo@bar.com');

        self::assertEquals([
            'template' => 'foo',
            'bcc' => 'foo@bar.com',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingCc(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->cc('foo@bar.com');

        self::assertEquals([
            'template' => 'foo',
            'cc' => 'foo@bar.com',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingEncodedParam(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->param('bar', 42);

        self::assertEquals([
            'template' => 'foo',
            'v:bar' => '42',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingEncodedParams(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->param('bar', 42)
                ->param('baz', '42');

        self::assertEquals([
            'template' => 'foo',
            'v:bar' => '42',
            'v:baz' => '"42"',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingOptions(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->option('require-tls', true);

        self::assertEquals([
            'template' => 'foo',
            'o:require-tls' => true,
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingRecipient(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->to('foo@bar.com');

        self::assertEquals([
            'template' => 'foo',
            'to' => 'foo@bar.com',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingSender(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->from('foo@bar.com');

        self::assertEquals([
            'template' => 'foo',
            'from' => 'foo@bar.com',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingSubject(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->subject('bar');

        self::assertEquals([
            'template' => 'foo',
            'subject' => 'bar',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::toArray
     */
    public function testConvertsMessageToArrayIncludingTemplateVersion(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        $message->version('42');

        self::assertEquals([
            'template' => 'foo',
            't:version' => '42',
        ], $message->toArray());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::deliverAt
     */
    public function testDeliveryTimeSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertFalse($message->hasOption('deliverytime'));
        $message->deliverAt('2022-07-13T10:27:13');
        self::assertTrue($message->hasOption('deliverytime'));
        self::assertSame(
            (new DateTime('2022-07-13T10:27:13'))
                ->format(DateTimeInterface::RFC2822),
            $message->getOptions()['deliverytime']
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::deliverAt
     */
    public function testDeliveryTimeSettingWithDateTime(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertFalse($message->hasOption('deliverytime'));
        $message->deliverAt(new DateTimeImmutable('2022-07-13T10:27:13'));
        self::assertTrue($message->hasOption('deliverytime'));
        self::assertSame(
            (new DateTime('2022-07-13T10:27:13'))
                ->format(DateTimeInterface::RFC2822),
            $message->getOptions()['deliverytime']
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::deliverAt
     */
    public function testDeliveryTimeSettingWithTimeZone(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertFalse($message->hasOption('deliverytime'));
        $message->deliverAt(new DateTimeImmutable('2022-07-13T10:27:13'), 'America/Anguilla');
        self::assertTrue($message->hasOption('deliverytime'));
        self::assertSame(
            (new DateTime('2022-07-13T06:27:13-0400')
            )->format(DateTimeInterface::RFC2822),
            $message->getOptions()['deliverytime']
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::deliverAt
     */
    public function testDeliveryTimeSettingWithTimeZoneInstance(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertFalse($message->hasOption('deliverytime'));
        $message->deliverAt(
            new DateTimeImmutable('2022-07-13T10:27:13'),
            new DateTimeZone('America/Anguilla')
        );
        self::assertTrue($message->hasOption('deliverytime'));
        self::assertSame(
            (new DateTime('2022-07-13T06:27:13-0400'))
                ->format(DateTimeInterface::RFC2822),
            $message->getOptions()['deliverytime']
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setDomain
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getDomain
     */
    public function testDomainSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getDomain());
        $message->via('foo');
        self::assertSame('foo', $message->getDomain());
        self::assertTrue($message->hasDomain());
        $message->setDomain('bar');
        self::assertSame('bar', $message->getDomain());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::for
     */
    public function testForHelperCreatesMessagesWithTemplateNameSet(): void
    {
        $message = MailgunTemplatedMessage::for('foo');
        self::assertSame('foo', $message->getTemplateName());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getOptions
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::addOption
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::hasOption
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::removeOption
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::withoutOption
     */
    public function testOptionRemoval(): void
    {
        $message = new MailgunTemplatedMessage('foo');

        self::assertEmpty($message->getOptions());
        $message->addOption('foo', 'bar');
        $message->addOption('baz', 'quz');
        self::assertNotEmpty($message->getOptions());
        self::assertSame('bar', $message->getOptions()['foo']);
        self::assertSame('quz', $message->getOptions()['baz']);
        $message->withoutOption('foo');
        self::assertNull($message->getOptions()['foo'] ?? null);
        self::assertFalse($message->hasOption('foo'));
        $message->removeOption('baz');
        self::assertNull($message->getOptions()['baz'] ?? null);
        self::assertFalse($message->hasOption('baz'));
        self::assertEmpty($message->getOptions());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getOptions
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::option
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::addOption
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::hasOption
     */
    public function testOptionSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');

        self::assertEmpty($message->getOptions());
        $message->option('foo', 'bar');
        $message->addOption('baz', 'quz');
        self::assertNotEmpty($message->getOptions());
        self::assertSame('bar', $message->getOptions()['foo']);
        self::assertSame('quz', $message->getOptions()['baz']);
        self::assertTrue($message->hasOption('foo'));
        self::assertTrue($message->hasOption('baz'));
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getParams
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::addParam
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::hasParam
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::removeParam
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::withoutParam
     */
    public function testParamRemoval(): void
    {
        $message = new MailgunTemplatedMessage('foo');

        self::assertEmpty($message->getParams());
        $message->addParam('foo', 'bar');
        $message->addParam('baz', 'quz');
        self::assertNotEmpty($message->getParams());
        self::assertSame('bar', $message->getParams()['foo']);
        self::assertSame('quz', $message->getParams()['baz']);
        $message->withoutParam('foo');
        self::assertNull($message->getParams()['foo'] ?? null);
        self::assertFalse($message->hasParam('foo'));
        $message->removeParam('baz');
        self::assertNull($message->getParams()['baz'] ?? null);
        self::assertFalse($message->hasParam('baz'));
        self::assertEmpty($message->getParams());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getParams
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::param
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::addParam
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::hasParam
     */
    public function testParamSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');

        self::assertEmpty($message->getParams());
        $message->param('foo', 'bar');
        $message->addParam('baz', 'quz');
        self::assertNotEmpty($message->getParams());
        self::assertSame('bar', $message->getParams()['foo']);
        self::assertSame('quz', $message->getParams()['baz']);
        self::assertTrue($message->hasParam('foo'));
        self::assertTrue($message->hasParam('baz'));
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setRecipient
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getRecipient
     */
    public function testRecipientSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getRecipient());
        $message->to('foo');
        self::assertSame('foo', $message->getRecipient());
        self::assertTrue($message->hasRecipient());
        $message->setRecipient('bar');
        self::assertSame('bar', $message->getRecipient());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setSender
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getSender
     */
    public function testSenderSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getSender());
        $message->from('foo');
        self::assertSame('foo', $message->getSender());
        $message->setSender('bar');
        self::assertSame('bar', $message->getSender());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::__construct
     */
    public function testSetsTemplateNameAndParamsOnConstruction(): void
    {
        $message = new MailgunTemplatedMessage('foo', [
            'foo' => 10,
            'bar' => 20,
            'baz' => 30,
        ]);
        self::assertSame('foo', $message->getTemplateName());
        self::assertEquals([
            'foo' => 10,
            'bar' => 20,
            'baz' => 30,
        ], $message->getParams());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::__construct
     */
    public function testSetsTemplateNameOnConstruction(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertSame('foo', $message->getTemplateName());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setSubject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getSubject
     */
    public function testSubjectSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getSubject());
        $message->subject('foo');
        self::assertSame('foo', $message->getSubject());
        $message->setSubject('bar');
        self::assertSame('bar', $message->getSubject());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::subject
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::setTemplateVersion
     * @covers \Matchory\MailgunTemplatedMessages\Messages\MailgunTemplatedMessage::getTemplateVersion
     */
    public function testVersionSetting(): void
    {
        $message = new MailgunTemplatedMessage('foo');
        self::assertNull($message->getTemplateVersion());
        $message->version('42');
        self::assertSame('42', $message->getTemplateVersion());
        $message->setTemplateVersion('43');
        self::assertSame('43', $message->getTemplateVersion());
    }
}