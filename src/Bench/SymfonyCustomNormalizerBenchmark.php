<?php

declare(strict_types=1);

namespace PhpSerializers\Benchmarks\Bench;

use PhpSerializers\Benchmarks\AbstractBench;
use PhpSerializers\Benchmarks\Model\Category;
use PhpSerializers\Benchmarks\Model\Comment;
use PhpSerializers\Benchmarks\Model\Forum;
use PhpSerializers\Benchmarks\Model\Thread;
use PhpSerializers\Benchmarks\Model\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * @author BoShurik <boshurik@gmail.com>
 */
class SymfonyCustomNormalizerBenchmark extends AbstractBench
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function initSerializer(): void
    {
        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new UserNormalizer(),
                new CommentNormalizer(),
                new CategoryNormalizer(),
                new ThreadNormalizer(),
                new ForumNormalizer(),
            ],
            [new JsonEncoder()]
        );
    }

    public function serialize(Forum $data): void
    {
        $this->serializer->serialize($data, 'json');
    }

    public function getPackageName(): string
    {
        return 'symfony/serializer';
    }

    public function getNote(): string
    {
        return <<<'NOTE'
Serialize object graphs using custom normalizers
NOTE;
    }
}

class CategoryNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param Category $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'parent' => $this->normalizer->normalize($object->getParent(), $format, $context),
            'children' => $this->normalizer->normalize($object->getChildren(), $format, $context),
            'createdAt' => $this->normalizer->normalize($object->getCreatedAt(), $format, $context),
            'updatedAt' => $this->normalizer->normalize($object->getUpdatedAt(), $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Category;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class CommentNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param Comment $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'content' => $object->getContent(),
            'author' => $this->normalizer->normalize($object->getAuthor(), $format, $context),
            'createdAt' => $this->normalizer->normalize($object->getCreatedAt(), $format, $context),
            'updatedAt' => $this->normalizer->normalize($object->getUpdatedAt(), $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Comment;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class ForumNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param Forum $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'category' => $this->normalizer->normalize($object->getCategory(), $format, $context),
            'threads' => $this->normalizer->normalize($object->getThreads(), $format, $context),
            'createdAt' => $this->normalizer->normalize($object->getCreatedAt(), $format, $context),
            'updatedAt' => $this->normalizer->normalize($object->getUpdatedAt(), $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Forum;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class ThreadNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param Thread $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'popularity' => $object->getPopularity(),
            'comments' => $this->normalizer->normalize($object->getComments(), $format, $context),
            'createdAt' => $this->normalizer->normalize($object->getCreatedAt(), $format, $context),
            'updatedAt' => $this->normalizer->normalize($object->getUpdatedAt(), $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Thread;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class UserNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param User $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->jsonSerialize();
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}