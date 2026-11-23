<?php

namespace Filafly\DetachedSlideOvers;

class DetachedSlideOvers
{
    /**
     * On the modal window of a slide-over that floats off the screen edge.
     */
    public const DETACHED_CLASS = 'fi-detached-slide-over';

    /**
     * On the modal window of a slide-over that stays on the edge, even when
     * the plugin detaches every slide-over in the panel.
     */
    public const ATTACHED_CLASS = 'fi-attached-slide-over';

    /**
     * On a hidden marker at the start of the `<body>` of a panel where
     * every slide-over is detached.
     */
    public const EVERYWHERE_CLASS = 'fi-detached-slide-overs';
}
