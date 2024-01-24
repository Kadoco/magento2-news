<?php
declare(strict_types=1);

namespace Kadoco\News\Console\Command;

use Magento\Framework\App\State as AppState;
use Kadoco\News\Model\NewsConfigProvider;
use Kadoco\News\Service\AssignNewProducts as AssignProductsToNewService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssignNewProducts extends Command
{
    /**
     * @var AppState
     */
    private AppState $appState;
    /**
     * @var AssignProductsToNewService
     */
    private AssignProductsToNewService $assignNewProducts;

    public function __construct(
        AppState $appState,
        AssignProductsToNewService $assignNewProducts,
        string $name = null
    ) {
        parent::__construct($name);

        $this->appState = $appState;
        $this->assignNewProducts = $assignNewProducts;
    }

    protected function configure()
    {
        $this->setName('kadoco:assignNews')
            ->setDescription('Assign news product to news category');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = microtime(true);
        $this->appState->setAreaCode('adminhtml');

        $output->writeln("\t<comment>Assigning products to brands</comment>");

        $output->writeln("\n\t\t<comment>AssigningProducts</comment>");
        $this->assignNewProducts->execute();

        $executionTime = microtime(true) - $time;
        $output->writeln("\t<comment>... completed in $executionTime seconds</comment>\n");
    }
}
