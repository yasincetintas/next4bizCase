<?php


namespace App\Entity;


use App\Traits\EntityDateInformationTrait;
use App\Traits\SoftDeletableTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DateLogRepository")
 * @ORM\Table(name="date_log", schema="renta", indexes={@ORM\Index(name="email", columns={"email"}),@ORM\Index(name="send_date", columns={"send_date"}),@ORM\Index(name="id", columns={"id"})})
 * @ORM\HasLifecycleCallbacks
 */
class DateLog
{
    use EntityDateInformationTrait;
    use SoftDeletableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var integer
     *
     * @ORM\Column(name="email",type="integer",nullable=false)
     */
    private $email;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="send_date",type="datetime",nullable=false)
     */
    private $sendDate;

    /**
     * @return int
     */
    public function getEmail(): int
    {
        return $this->email;
    }

    /**
     * @param int $email
     */
    public function setEmail(int $email): void
    {
        $this->email = $email;
    }

    /**
     * @return DateTime
     */
    public function getSendDate(): DateTime
    {
        return $this->sendDate;
    }

    /**
     * @param DateTime $sendDate
     */
    public function setSendDate(DateTime $sendDate): void
    {
        $this->sendDate = $sendDate;
    }
}