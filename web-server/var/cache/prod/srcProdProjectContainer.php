<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerXHNOwBe\srcProdProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerXHNOwBe/srcProdProjectContainer.php') {
    touch(__DIR__.'/ContainerXHNOwBe.legacy');

    return;
}

if (!\class_exists(srcProdProjectContainer::class, false)) {
    \class_alias(\ContainerXHNOwBe\srcProdProjectContainer::class, srcProdProjectContainer::class, false);
}

return new \ContainerXHNOwBe\srcProdProjectContainer(array(
    'container.build_hash' => 'XHNOwBe',
    'container.build_id' => 'e98116dd',
    'container.build_time' => 1541178209,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerXHNOwBe');