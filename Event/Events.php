<?php

namespace ZQ\SunSearchBundle\Event;

/**
 * List of event which can be fired
 */
final class Events
{
    const PRE_INSERT = 'sunsearch.pre_insert';
    const POST_INSERT = 'sunsearch.post_insert';

    const PRE_UPDATE = 'sunsearch.pre_update';
    const POST_UPDATE = 'sunsearch.post_update';

    const PRE_DELETE = 'sunsearch.pre_delete';
    const POST_DELETE = 'sunsearch.post_delete';

    const PRE_CLEAR_INDEX = 'sunsearch.pre_clear_index';
    const POST_CLEAR_INDEX = 'sunsearch.post_clear_index';

    const ERROR = 'sunsearch.error';
} 