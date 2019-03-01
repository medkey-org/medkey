<?php
namespace app\port\cli\controllers;

use app\modules\incident\models\orm\EmailThreadMail;
use app\modules\incident\models\orm\EmailThreadTemplate;
use PhpMimeMailParser;
use app\common\console\Controller;
use app\modules\incident\application\MailServiceInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MailController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class MailController extends Controller
{
    /**
     * @var MailServiceInterface
     */
    public $mailService;

    /**
     * @param string $threadName
     * @param string $threadDescription
     * @param string $pathEml path to .eml files
     * @return null
     */
    public function actionUploadEml($threadName, $threadDescription, $pathEml)
    {
        $emailThreadTemplate = EmailThreadTemplate::find()
            ->where([
                'name' => $threadName
            ])
            ->one();
        if (is_null($emailThreadTemplate)) {
            $emailThreadTemplate = new EmailThreadTemplate();
            $emailThreadTemplate->name = $threadName;
            $emailThreadTemplate->description = $threadDescription;
            $emailThreadTemplate->save();
        }
        if (!is_dir($pathEml)) {
            $this->line('Given path is not directory.');
            return null;
        }
        $finder = new Finder();
        $files = $finder->files()->in($pathEml);
        foreach ($files as $file) {
            /** @var $file SplFileInfo */
            if ($file->getExtension() === 'eml') {
                $parser = new PhpMimeMailParser\Parser();
                $parser->setPath($file->getRealPath());
                $tempDir = tempnam(sys_get_temp_dir(), 'mail-attach-');
                unlink($tempDir);
                mkdir($tempDir);
                $parser->saveAttachments($tempDir, false);
                $attachment = $parser->getAttachments(false);
                $emailThread = new EmailThreadMail();
                $emailThread->email_thread_template_id = $emailThreadTemplate->id;
                $emailThread->email_date = $parser->getHeader('Date');
                $emailThread->mail_from = $parser->getHeader('from');
                $emailThread->mail_to = $parser->getHeader('to');
                $emailThread->subject = $parser->getHeader('subject');
                $emailThread->body = $parser->getMessageBody();
                $emailThread->attach = !empty($attachment[0]) ? stream_get_contents($attachment[0]->getStream()) : null;
                $emailThread->attach_filename = !empty($attachment[0]) ? $attachment[0]->getFilename() : null;
                $emailThread->save();
            }
        }
    }

    /**
     * MailController constructor.
     * @param $id
     * @param $module
     * @param MailServiceInterface $mailService
     * @param array $config
     */
    public function __construct($id, $module, MailServiceInterface $mailService, array $config = [])
    {
        $this->mailService = $mailService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Generates and push emails to an IMAP server.
     * @param int $virtualUserId id of the virtual user to generate emails for
     */
    public function actionUpload(?int $virtualUserId = null)
    {
        $this->mailService->upload($virtualUserId);
    }

    /**
     * Creates new email account on an IMAP server.
     * @param string $name a name for a new account
     * @param string $password and a password for it
     */
    public function actionCreateAccount(string $name, string $password)
    {
        $this->mailService->createAccount($name, $password);
    }

    /**
     * @param string $name
     * @param string $password
     */
    public function actionProdCreateAccount(string $name, string $password)
    {
        $this->mailService->createProdAccount($name, $password);
    }
}
