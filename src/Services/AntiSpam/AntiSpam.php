<?php


namespace App\Services\AntiSpam;


use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;

class AntiSpam
{

    private $fordiddenStrings;
    private $logger;
    private $request_stack;

   public function __construct(array $forbiddenStrings, LoggerInterface $logger, RequestStack $request_stack)
   {
        $this->fordiddenStrings = $forbiddenStrings;
        $this->logger = $logger;
        $this->request_stack =$request_stack;
   }

    public function isSpam(string $myString):bool
    {
        foreach ($this->fordiddenStrings as $fordiddenString){
            if (strstr($myString, $fordiddenString)){
                $this->logger->info('Une chaine indésirable a été trouvée ! ');
                $this->request_stack->getCurrentRequest()->getClientIp();
                return true;
                break;
            }
        }
        return false;
    }
}