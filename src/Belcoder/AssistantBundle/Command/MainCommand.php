<?php

namespace Belcoder\AssistantBundle\Command;

use Belcoder\AssistantBundle\Handler\AppKernelHandler;
use Belcoder\AssistantBundle\Handler\ComposerRequireHandler;
use Belcoder\AssistantBundle\Handler\ConfigHandler;
use Belcoder\AssistantBundle\Handler\CRUDHandler;
use Belcoder\AssistantBundle\Handler\MappingHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MainCommand extends ContainerAwareCommand
{
    private $path_composer = '';
    private $path_config = '';
    private $path_kernel = '';
    private $path_entities = '';
    private $path_app = '';
    private $bundle = '';

    protected function configure()
    {
        $this
            ->setName('belcoder:assistant')
            ->setDescription('...')
            ->setHelp("...");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bundle = 'AppBundle';

        $this->path_app = $this->getContainer()->getParameter('kernel.root_dir');

        $this->path_composer = $this->path_app . '/../composer.json';
        $this->path_config = $this->path_app . '/config/config.yml';
        $this->path_kernel = $this->path_app . '/AppKernel.php';
        $this->path_entities = $this->path_app . '/../src/' . $this->bundle . '/Entity/';

        $answer = $this->query('Что новый хозяин надо?', [
            1 => 'Я только что установил симфони, сделай настройку',
            2 => 'У меня есть сущность, хочу Controller, Repository и Routing',
            3 => 'Хочу связать две сущности (Association Mappings)',
            4 => 'Выйти'
        ]);

        echo PHP_EOL;

        switch ($answer['key']) {
            case 1:
                echo $this->colorize(' ? ', 'CYAN') .
                    ' Сейчас я добавлю в composer.json необходимые зависимости.' .
                    ' Перезапишу файл config.yml, в котором будут рекомендуемые настройки.' .
                    ' Перезапишу файл AppKernel.php, в котором будет добавлен часовой пояс, ' .
                    'и все новые бандлы, которые были добавлены в composer.json' . PHP_EOL;

                echo PHP_EOL;

                readline('Нажмите Enter для продолжения');
                $this->queryAfterInstallation();
                break;

            case 2:
                echo $this->colorize(' ? ', 'CYAN') .
                    ' описание того что я сейчас буду делать...' . PHP_EOL;

                echo PHP_EOL;

                readline('Нажмите Enter для продолжения');
                $this->generateCRUD();
                break;

            case 3:
                echo $this->colorize(' ? ', 'CYAN') .
                    ' описание того что я сейчас буду делать...' . PHP_EOL;

                echo PHP_EOL;

                readline('Нажмите Enter для продолжения');
                $this->generateAssociationMappings();
                break;

            default:
                // exit
                break;
        }

        echo PHP_EOL;
    }

    private function generateAssociationMappings()
    {
        $first_entity = $this->query('Какая сущность будет первая?', $this->getEntity());
        $first_field = readline('Новое поле: ');
        $second_entity = $this->query('Какая сущность будет вторая?', $this->getEntity());
        $second_field = readline('Новое поле: ');

        $mapping = $this->query('Как будем делать связь?', [
            1 => 'Many-To-One, Unidirectional',
            2 => 'One-To-One, Unidirectional',
            3 => 'One-To-One, Bidirectional',
            4 => 'One-To-One, Self-referencing',
            5 => 'One-To-Many, Bidirectional',
            6 => 'One-To-Many, Unidirectional with Join Table',
            7 => 'One-To-Many, Self-referencing',
            8 => 'Many-to-Many, Unidirectional',
            9 => 'Many-to-Many, Bidirectional'
        ]);

        $changes = MappingHandler::createChanges(
            $mapping['value'],
            $first_entity['value'],
            $second_entity['value'],
            $first_field,
            $second_field
        );

        if ($changes[0]) {
            echo PHP_EOL;
            echo $this->colorize(' ! ', 'CYAN') .
                ' Изменения в сущности ' . $first_entity['value'] . ':' . PHP_EOL;
            echo PHP_EOL;
            echo $changes[0];

            echo PHP_EOL;
            echo PHP_EOL;
        }

        if ($changes[1]) {
            echo $this->colorize(' ! ', 'CYAN') .
                ' Изменения в сущности ' . $second_entity['value'] . ':' . PHP_EOL;
            echo PHP_EOL;
            echo $changes[1];

            echo PHP_EOL;
            echo PHP_EOL;
        }

        readline('Нажмите Enter для продолжения');

        try {
            if ($changes[0]) {
                $file_entity_first = $this->path_entities . $first_entity['value'] . '.php';
                MappingHandler::writeMarker($file_entity_first);
                $content_file_entity_first = file_get_contents($file_entity_first);
                $content_file_entity_first = str_replace('// *', '// *' . "\n\n" . $changes[0], $content_file_entity_first);
                file_put_contents($file_entity_first, $content_file_entity_first);
            }

            if ($changes[1]) {
                $file_entity_second = $this->path_entities . $second_entity['value'] . '.php';
                MappingHandler::writeMarker($file_entity_second);
                $content_file_entity_second = file_get_contents($file_entity_second);
                $content_file_entity_second = str_replace('// *', '// *' . "\n\n" . $changes[1], $content_file_entity_second);
                file_put_contents($file_entity_second, $content_file_entity_second);
            }
        } catch (\Exception $e) {
            throw new \Exception('Error write in entity files');
        }

        echo PHP_EOL;
        echo $this->colorize('Успешно!', 'GREEN') . PHP_EOL;
        echo PHP_EOL;
        echo $this->colorize(' ! ', 'CYAN') .
            ' Запусти следующие команды:' . PHP_EOL;
        echo '    - bin/console doctrine:generate:entities ' . $this->bundle . ':' . $first_entity['value'] . PHP_EOL;
        echo '    - bin/console doctrine:generate:entities ' . $this->bundle . ':' . $second_entity['value'] . PHP_EOL;
        echo '    - bin/console doctrine:migrations:diff' . PHP_EOL;
        echo '    - bin/console doctrine:migrations:migrate' . PHP_EOL;
    }

    /**
     * @return array
     */
    private function getEntity()
    {
        $entities = scandir($this->path_entities);
        unset($entities[0]);
        unset($entities[1]);

        $a = 1;
        $b = [];
        foreach ($entities as $key => $value) {
            if (preg_match('!\~$!Ui', $value)) {
                continue;
            }

            $value = str_replace('.php', '', $value);
            $b[$a] = $value;
            $a++;
        }

        return $b;
    }

    /**
     * @throws \Exception
     */
    private function generateCRUD()
    {
        $answer = $this->query('Какую сущность будем крудить?', $this->getEntity());

        echo PHP_EOL;

        $repository = CRUDHandler::getRepository($answer['value']);
        $controller = CRUDHandler::getController($answer['value']);
        $routing = CRUDHandler::getRouting($answer['value']);

        $path_repository = $this->path_app . '/../src/' .
            $this->bundle . '/Repository/' . $answer['value'] . 'Repository.php';
        $path_controller = $this->path_app . '/../src/' .
            $this->bundle . '/Controller/' . $answer['value'] . 'RESTController.php';
        $path_routing = $this->path_app . '/config/routing.yml';

        echo $this->colorize(
            ' Сейчас я создам/отредактирую три файла: ',
            'YELLOW'
        ) . PHP_EOL;

        echo PHP_EOL;

        echo ' ' . $path_repository . PHP_EOL;
        echo ' ' . $path_controller . PHP_EOL;
        echo ' ' . $path_routing . PHP_EOL;

        echo PHP_EOL;

        readline('Нажмите Enter для продолжения');

        try {
            file_put_contents($path_controller, $controller);
            file_put_contents($path_repository, $repository);
            file_put_contents($path_routing, "\n\n" . $routing, FILE_APPEND);
        } catch (\Exception $e) {
            throw new \Exception('Error write in CRUD files');
        }

        echo PHP_EOL;
        echo $this->colorize('Успешно!', 'GREEN') . PHP_EOL;
        echo PHP_EOL;
        echo $this->colorize(' ! ', 'CYAN') .
            ' Теперь нужно внести изменения в БД, запусти следующие команды:' . PHP_EOL;
        echo '    - bin/console doctrine:migrations:diff' . PHP_EOL;
        echo '    - bin/console doctrine:migrations:migrate' . PHP_EOL;
    }

    private function queryAfterInstallation()
    {
        echo PHP_EOL;
        $this->editComposerJson();
        echo PHP_EOL;
        $this->editConfigYml();
        echo PHP_EOL;
        $this->editAppKernel();
    }

    /**
     * @throws \Exception
     */
    private function editAppKernel()
    {
        echo $this->colorize(
            ' Файл AppKernel.php будет полностью перезаписан, продолжаем? ',
            'YELLOW'
        ) . PHP_EOL;

        echo PHP_EOL;

        readline('Нажмите Enter для продолжения');

        try {
            file_put_contents($this->path_kernel, AppKernelHandler::getBody());
        } catch (\Exception $e) {
            throw new \Exception('Error write in AppKernel.php');
        }

        echo PHP_EOL;
        echo $this->colorize('Успешно!', 'GREEN') . PHP_EOL;
    }

    /**
     * @throws \Exception
     */
    private function editConfigYml()
    {
        echo $this->colorize(
            ' Файл config.yml будет полностью перезаписан, продолжаем? ',
            'YELLOW'
        ) . PHP_EOL;

        echo PHP_EOL;

        readline('Нажмите Enter для продолжения');

        try {
            file_put_contents($this->path_config, ConfigHandler::getBody());
        } catch (\Exception $e) {
            throw new \Exception('Error write in config.yml');
        }

        echo PHP_EOL;
        echo $this->colorize('Успешно!', 'GREEN') . PHP_EOL;
    }

    /**
     * @throws \Exception
     */
    private function editComposerJson()
    {
        $composer_require = ComposerRequireHandler::getRequire();

        $content_composer = file_get_contents($this->path_composer);
        $composer = json_decode($content_composer, true);

        $require = $composer['require'];
        $new_require = [];

        foreach ($composer_require as $key_a => $value_a) {
            $need_add = true;
            foreach ($composer['require'] as $key_b => $value_b) {
                if ($key_a == $key_b) {
                    $need_add = false;
                    break;
                }
            }

            if (!$need_add) {
                continue;
            }

            $new_require[$key_a] = $value_a;
        }

        if ($new_require) {
            echo $this->colorize(
                ' Следующие зависимости будут записаны в файл composer.json: ',
                'YELLOW'
            ) . PHP_EOL;

            echo PHP_EOL;

            foreach ($new_require as $key => $value) {
                echo ' ' . $key . ':' . $value . PHP_EOL;
            }
        } else {
            echo $this->colorize(
                ' В файле composer.json уже присутствуют все необходимые зависимости ',
                'YELLOW'
            ) . PHP_EOL;
        }

        echo PHP_EOL;

        readline('Нажмите Enter для продолжения');

        if ($new_require) {
            $require = array_merge($require, $new_require);
            $composer['require'] = $require;
            try {
                $content_composer = json_encode($composer, JSON_PRETTY_PRINT);
                $content_composer = str_replace('\/', '/', $content_composer);
                file_put_contents($this->path_composer, $content_composer);
            } catch (\Exception $e) {
                throw new \Exception('Error write in composer.json');
            }

            echo PHP_EOL;
            echo $this->colorize('Успешно!', 'GREEN') . PHP_EOL;
        }
    }

    /**
     * @param $title
     * @param $options
     * @param bool $show_options
     * @return mixed
     * @throws \Exception
     */
    private function query($title, $options, $show_options = true)
    {
        if (is_array($options) && count($options) >= 1) {
            //
        } else {
            throw new \Exception('No answer choices!');
        }

        if ($show_options) {
            echo PHP_EOL;
            echo $this->colorize(' == ' . $title . ' == ', 'MAGENTA') . PHP_EOL;
            echo PHP_EOL;

            foreach ($options as $key => $value) {
                echo '[' . $key . ']';
                echo ' ' . $value . PHP_EOL;
            }
        }

        echo PHP_EOL;
        $answer = readline('Ваш выбор: ');

        if (!isset($options[$answer])) {
            echo $this->colorize('Попробуй еще раз', 'RED') . PHP_EOL;
            return $this->query($title, $options, false);
        } else {
            return [
                'key' => $answer,
                'value' => $options[$answer]
            ];
        }
    }

    /**
     * @param $text
     * @param $color
     * @return string
     * @throws \Exception
     */
    private function colorize($text, $color)
    {
        switch ($color) {
            case 'GREEN':
                $out = '[42m';
                break;
            case 'RED':
                $out = '[41m';
                break;
            case 'YELLOW':
                $out = '[43m';
                break;
            case 'NOTE':
                $out = '[44m';
                break;
            case 'CYAN':
                $out = '[46m';
                break;
            case 'MAGENTA':
                $out = '[45m';
                break;
            default:
                throw new \Exception('Invalid color: ' . $color);
        }
        return chr(27) . $out . $text . chr(27) . '[0m';
    }
}
