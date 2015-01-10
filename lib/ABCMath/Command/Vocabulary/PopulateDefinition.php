<?php
namespace ABCMath\Command\Vocabulary;

use ABCMath\Vocabulary\WordManager;
use ABCMath\Vocabulary\Word;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDefinition extends Command
{
    protected function configure()
    {
        $this->setName('do_populate')
        ->setDescription('Populate words without definitions with definitions.')
        ->addArgument('max_populate_count',
            InputArgument::REQUIRED,
            'Maximum number of words to populate.'
        )
        ->addArgument('dictionary',
            InputArgument::REQUIRED,
            'MerriamWebster|DictionaryDotCom'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $max = $input->getArgument('max_populate_count');
        $dictionarySource = $input->getArgument('dictionary');

        $wordManager = new WordManager();
        $words = $wordManager->getWordsWithoutDefinition($max);

        $source = "\ABCMath\Vocabulary\Dictionary\\{$dictionarySource}";

        if (!count($words)) {
            $output->writeln("All words are populated.");

            return;
        }

        $success = 0;
        $failure = 0;

        foreach ($words as $w) {
            $word = new Word();
            $word->load($w);

            $output->writeln("Word {$word->word} loaded successfully.");

            if (class_exists($source)) {
                $dictionary = new $source($word);
            } else {
                $error = "Unable to retrieve dictionary. Bad Class: [{$source}]";
                $output->writeln($error);

                return;
            }

            try {
                $dictionary->extractDefinition();
            } catch (InvalidArgumentException $e) {
                $output->writeln("Failed at extracting definition for [{$word->word}]. Error: ".$e->getMessage());
                $failure += 1;
                continue;
            }

            $definitions = array();
            if (count($dictionary->definitions)) {
                foreach ($dictionary->definitions as $d) {
                    $word->addDefinition($d);
                }
            } else {
                $output->writeln("No definition found for [{$word->word}].");
                $failure += 1;
                continue;
            }

            try {
                $word->transaction = true;
                $word->save();
            } catch (Exception $e) {
                $output->writeln("Failed at updating word [{$word->word}]. Error: ".$e->getMessage());
                $failure += 1;
                continue;
            }
            $success += 1;
            $output->writeln("Word [{$word->word}] updated successfully.");
        }
        $output->writeln("[{$success}] words successfully defined.");
        $output->writeln("[{$failure}] words failed.");
    }
}
