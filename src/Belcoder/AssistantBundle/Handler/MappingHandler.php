<?php

namespace Belcoder\AssistantBundle\Handler;

class MappingHandler
{
    public static function createChanges($mapping, $first_entity, $second_entity, $first_field, $second_field)
    {
        $first_changes = [];
        $second_changes = [];

        $first_field_ucf = ucfirst(strtolower($first_field));
        $second_field_ucf = ucfirst(strtolower($second_field));

        switch ($mapping) {
            case 'Many-To-One, Unidirectional':
                $first_changes[] = ' * @ORM\ManyToOne(targetEntity="' . $second_entity . '")';
                $first_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($second_entity) . '_id", referencedColumnName="id")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                break;

            case 'One-To-One, Unidirectional':
                $first_changes[] = ' * @ORM\OneToOne(targetEntity="' . $second_entity . '")';
                $first_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($second_entity) . '_id", referencedColumnName="id")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                break;

            case 'One-To-One, Bidirectional':
                $first_changes[] = ' * @ORM\OneToOne(targetEntity="' . $second_entity . '", mappedBy="' . $second_field_ucf . '")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                $second_changes[] = ' * @ORM\OneToOne(targetEntity="' . $first_entity . '", inversedBy="' . $first_field_ucf . '")';
                $second_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($first_entity) . '_id", referencedColumnName="id")';
                $second_changes[] = 'private $' . $second_field_ucf . ';';
                break;

            case 'One-To-One, Self-referencing':
                $first_changes[] = ' * @ORM\OneToOne(targetEntity="' . $second_entity . '")';
                $first_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($second_entity) . '_id", referencedColumnName="id")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                break;

            case 'One-To-Many, Bidirectional':
                $first_changes[] = ' * @ORM\OneToMany(targetEntity="' . $second_entity . '", mappedBy="' . $second_field_ucf . '")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                $second_changes[] = ' * @ORM\ManyToOne(targetEntity="' . $first_entity . '", inversedBy="' . $first_field_ucf . '")';
                $second_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($first_entity) . '_id", referencedColumnName="id")';
                $second_changes[] = 'private $' . $second_field_ucf . ';';
                break;

            case 'One-To-Many, Unidirectional with Join Table':
                $first_changes[] = ' * @ORM\ManyToMany(targetEntity="' . $second_entity . '")';
                $first_changes[] = ' * @ORM\JoinTable(name="' . strtolower($first_entity) . 's_' . strtolower($second_entity) . 's",';
                $first_changes[] = ' *     joinColumns={@ORM\JoinColumn(name="' . strtolower($first_entity) . '_id", referencedColumnName="id")},';
                $first_changes[] = ' *     inverseJoinColumns={@ORM\JoinColumn(name="' . strtolower($second_entity) . '_id", referencedColumnName="id", unique=true)}';
                $first_changes[] = ' * )';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                break;

            case 'One-To-Many, Self-referencing':
                $first_changes[] = ' * @ORM\OneToMany(targetEntity="' . $second_entity . '", mappedBy="' . $second_field_ucf . '")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                $second_changes[] = ' * @ORM\ManyToOne(targetEntity="' . $first_entity . '", inversedBy="' . $first_field_ucf . '")';
                $second_changes[] = ' * @ORM\JoinColumn(name="' . strtolower($first_entity) . '_id", referencedColumnName="id")';
                $second_changes[] = 'private $' . $second_field_ucf . ';';
                break;

            case 'Many-to-Many, Unidirectional':
                $first_changes[] = ' * @ORM\ManyToMany(targetEntity="' . $second_entity . '")';
                $first_changes[] = ' * @ORM\JoinTable(name="' . strtolower($first_entity) . 's_' . strtolower($second_entity) . 's",';
                $first_changes[] = ' *     joinColumns={@ORM\JoinColumn(name="' . strtolower($first_entity) . '_id", referencedColumnName="id")},';
                $first_changes[] = ' *     inverseJoinColumns={@ORM\JoinColumn(name="' . strtolower($second_entity) . '_id", referencedColumnName="id")}';
                $first_changes[] = ' * )';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                break;

            case 'Many-to-Many, Bidirectional':
                $first_changes[] = ' * @ORM\ManyToMany(targetEntity="' . $second_entity . '", inversedBy="' . $second_field_ucf . '")';
                $first_changes[] = ' * @ORM\JoinTable(name="' . strtolower($first_entity) . 's_' . strtolower($second_entity) . 's")';
                $first_changes[] = 'private $' . $first_field_ucf . ';';
                $second_changes[] = ' * @ORM\ManyToMany(targetEntity="' . $first_entity . '", mappedBy="' . $first_field_ucf . '")';
                $second_changes[] = 'private $' . $second_field_ucf . ';';
                break;
        }

        $first_changes_str = '';

        if ($first_changes) {
            $first_changes_str .= '    /**' . "\n";
            foreach ($first_changes as $key => $change) {
                if (count($first_changes) - 1 == $key) {
                    $first_changes_str .= '     */' . "\n";
                }
                $first_changes_str .= '    ' . $change . "\n";
            }
        }

        $second_changes_str = '';

        if ($second_changes) {
            $second_changes_str .= '    /**' . "\n";
            foreach ($second_changes as $key => $change) {
                if (count($second_changes) - 1 == $key) {
                    $second_changes_str .= '     */' . "\n";
                }
                $second_changes_str .= '    ' . $change . "\n";
            }
        }

        return [
            $first_changes_str,
            $second_changes_str
        ];
    }
}
