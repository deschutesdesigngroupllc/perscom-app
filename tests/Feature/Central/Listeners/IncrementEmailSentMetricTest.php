<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Listeners;

use App\Listeners\IncrementEmailSentMetric;
use App\Metrics\EmailSentMetric;
use App\Metrics\Metric;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tests\Feature\Central\CentralTestCase;

class IncrementEmailSentMetricTest extends CentralTestCase
{
    public function test_handling_the_event_increments_the_email_sent_metric(): void
    {
        $before = Metric::total(EmailSentMetric::class);

        (new IncrementEmailSentMetric)->handle($this->buildMessageSentEvent());

        $this->assertSame($before + 1, Metric::total(EmailSentMetric::class));
    }

    private function buildMessageSentEvent(): MessageSent
    {
        $email = (new Email)
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test Subject')
            ->text('Test body');

        $envelope = new Envelope(
            new Address('from@example.com'),
            [new Address('to@example.com')],
        );

        return new MessageSent(new SentMessage(new SymfonySentMessage($email, $envelope)));
    }
}
