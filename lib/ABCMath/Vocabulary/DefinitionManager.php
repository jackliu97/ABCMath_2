<?php
namespace ABCMath\Vocabulary;

use ABCMath\Meta\Implement\ElementList;
use ABCMath\Base;

class DefinitionManager extends Base implements ElementList
{
    public $word_id;
    public $definitions;

    public function __construct()
    {
        parent::__construct();
    }

    public function getRandomDefinitionsSQL($limit = 10)
    {
        $sql = "SELECT 	id,
						vocabulary_id,
						parts_of_speech,
						definition
				FROM vocabulary_definition
				GROUP BY vocabulary_id
				ORDER BY rand() LIMIT {$limit}";

        return $sql;
    }

    public function getRandomDefinitions($limit = 10)
    {
        $stmt = $this->_conn->prepare(
            $this->getRandomDefinitionsSQL($limit)
            );
        $stmt->execute();

        $result = $stmt->fetchAll();

        if (!count($result)) {
            return array();
        }

        $return = array();
        foreach ($result as $r) {
            $return[] = $r;
        }

        return $return;
    }

    public function getDefinitionsFromWord()
    {
        if ($this->word_id == null) {
            throw new \Exception('Word ID is needed to load a list.');
        }

        $qb = $this->_conn->createQueryBuilder();
        $qb->select('vd.id',
                    'vd.vocabulary_id as word_id',
                    'vd.word',
                    'vd.parts_of_speech',
                    'vd.definition')
            ->from('vocabulary_definition', 'vd')
            ->where('vd.vocabulary_id = ?')
            ->setParameter(0, $this->word_id);
        $definitions = $qb->execute()->fetchAll();

        if (!is_array($definitions) || !count($definitions)) {
            return;
        }

        foreach ($definitions as $def) {
            $definition = new Definition($def['id']);
            $definition->word_id = $def['word_id'];
            $definition->word = $def['word'];
            $definition->parts_of_speech = $def['parts_of_speech'];
            $definition->definition = $def['definition'];
            $this->definitions[] = $definition;
        }
    }
}
