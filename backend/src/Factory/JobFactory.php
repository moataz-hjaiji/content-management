<?php

namespace App\Factory;

use App\Entity\Job;
use App\Repository\JobRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Job>
 *
 * @method        Job|Proxy                     create(array|callable $attributes = [])
 * @method static Job|Proxy                     createOne(array $attributes = [])
 * @method static Job|Proxy                     find(object|array|mixed $criteria)
 * @method static Job|Proxy                     findOrCreate(array $attributes)
 * @method static Job|Proxy                     first(string $sortedField = 'id')
 * @method static Job|Proxy                     last(string $sortedField = 'id')
 * @method static Job|Proxy                     random(array $attributes = [])
 * @method static Job|Proxy                     randomOrCreate(array $attributes = [])
 * @method static JobRepository|RepositoryProxy repository()
 * @method static Job[]|Proxy[]                 all()
 * @method static Job[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Job[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Job[]|Proxy[]                 findBy(array $attributes)
 * @method static Job[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Job[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class JobFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'Title' => self::faker()->words(3,true),
            'content' => self::faker()->text(),
            'deadline' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Job $job): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Job::class;
    }
}
