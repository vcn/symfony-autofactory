<?php

use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vcn\Symfony\AutoFactory\Annotation\Bind;
use Vcn\Symfony\AutoFactory\Annotation\Id;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;
use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\AutoFactoryParser;
use Vcn\Symfony\AutoFactory\ContainerUtils;

require __DIR__ . '/../vendor/autoload.php';

class Person
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     *
     */
    public function introduceYourself(): void
    {
        echo $this->message;
    }
}

class Group
{
    /**
     * @var Person[]
     */
    private $persons;

    /**
     * @param Person[] $persons
     */
    public function __construct(Person ...$persons)
    {
        $this->persons = $persons;
    }

    /**
     *
     */
    public function introduceGroup()
    {
        foreach ($this->persons as $person) {
            $person->introduceYourself();
        }
    }
}

class Factory implements AutoFactory
{
    /**
     * @Id("person.dutch")
     *
     * @return Person
     */
    public static function createDutchPerson(): Person
    {
        return new Person("Hallo, ik ben Kees!\n");
    }

    /**
     * @Id("person.german")
     *
     * @return Person
     */
    public static function createGermanPerson(): Person
    {
        return new Person("Hallo, ich bin Friedhelm!\n");
    }

    /**
     * @IsPublic(true)
     *
     * @Bind(arg="$person1", id="person.dutch")
     * @Bind(arg="$person2", id="person.german")
     *
     * @param Person $person1
     * @param Person $person2
     *
     * @return Group
     */
    public static function createGroup(Person $person1, Person $person2): Group
    {
        return new Group($person1, $person2);
    }
}


$parser    = new AutoFactoryParser();
$container = new ContainerBuilder();

$serviceDefinitions = $parser->parse(Factory::class);

foreach ($serviceDefinitions as $serviceDefinition) {
    ContainerUtils::register($serviceDefinition, $container);
}

$compiler = new Compiler();
$compiler->compile($container);

/* @var Group $group */
$group = $container->get(Group::class);

$group->introduceGroup();
