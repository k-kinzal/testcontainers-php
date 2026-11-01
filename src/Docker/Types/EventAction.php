<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Utility\Ensure;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

/**
 * Represents the action of a Docker event.
 * @api
 */
class EventAction implements Stringable
{
    use Ensure;

    /**
     * The action emitted when an object is created.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $CREATE = 'create';

    /**
     * The action emitted when a container is started.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $START = 'start';

    /**
     * The action emitted when a container is restarted.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $RESTART = 'restart';

    /**
     * The action emitted when a container is stopped.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $STOP = 'stop';

    /**
     * The action emitted when a checkpoint of a container is created.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $CHECKPOINT = 'checkpoint';

    /**
     * The action emitted when a container is paused.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $PAUSE = 'pause';

    /**
     * The action emitted when a container is unpaused.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $UNPAUSE = 'unpause';

    /**
     * The action emitted when a client attaches to a container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $ATTACH = 'attach';

    /**
     * The action emitted when a client detaches from a container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DETACH = 'detach';

    /**
     * The action emitted when the TTY of a container is resized.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $RESIZE = 'resize';

    /**
     * The action emitted when an object is updated.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $UPDATE = 'update';

    /**
     * The action emitted when a container is renamed.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $RENAME = 'rename';

    /**
     * The action emitted when a signal is sent to a container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $KILL = 'kill';

    /**
     * The action emitted when the main process of a container exits.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DIE = 'die';

    /**
     * The action emitted when a container runs out of memory.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $OOM = 'oom';

    /**
     * The action emitted when an object is destroyed.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DESTROY = 'destroy';

    /**
     * The action emitted when an object is removed.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $REMOVE = 'remove';

    /**
     * The action emitted when a container is committed to an image.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $COMMIT = 'commit';

    /**
     * The action emitted when the processes of a container are listed.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $TOP = 'top';

    /**
     * The action emitted when files are copied from or to a container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $COPY = 'copy';

    /**
     * The action emitted when a path of a container is archived.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $ARCHIVE_PATH = 'archive-path';

    /**
     * The action emitted when an archive is extracted into a container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXTRACT_TO_DIR = 'extract-to-dir';

    /**
     * The action emitted when a container is exported.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXPORT = 'export';

    /**
     * The action emitted when an image is imported.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $IMPORT = 'import';

    /**
     * The action emitted when an image is saved.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $SAVE = 'save';

    /**
     * The action emitted when an image is loaded.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $LOAD = 'load';

    /**
     * The action emitted when an image is tagged.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $TAG = 'tag';

    /**
     * The action emitted when an image is untagged.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $UNTAG = 'untag';

    /**
     * The action emitted when an image is pushed.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $PUSH = 'push';

    /**
     * The action emitted when an image is pulled.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $PULL = 'pull';

    /**
     * The action emitted when objects are pruned.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $PRUNE = 'prune';

    /**
     * The action emitted when an image is deleted.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DELETE = 'delete';

    /**
     * The action emitted when a plugin is enabled.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $ENABLE = 'enable';

    /**
     * The action emitted when a plugin is disabled.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DISABLE = 'disable';

    /**
     * The action emitted when a container is connected to a network.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $CONNECT = 'connect';

    /**
     * The action emitted when a container is disconnected from a network.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DISCONNECT = 'disconnect';

    /**
     * The action emitted when the daemon configuration is reloaded.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $RELOAD = 'reload';

    /**
     * The action emitted when a volume is mounted.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $MOUNT = 'mount';

    /**
     * The action emitted when a volume is unmounted.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $UNMOUNT = 'unmount';

    /**
     * The action emitted when an exec instance is created. It is commonly followed by `: ` and the command of the exec.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXEC_CREATE = 'exec_create';

    /**
     * The action emitted when an exec instance is started. It is commonly followed by `: ` and the command of the exec.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXEC_START = 'exec_start';

    /**
     * The action emitted when the process of an exec instance exits.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXEC_DIE = 'exec_die';

    /**
     * The action emitted when a client detaches from an exec instance.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $EXEC_DETACH = 'exec_detach';

    /**
     * The action emitted when the health status of a container changes. It is followed by `: ` and the health status.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $HEALTH_STATUS = 'health_status';

    /**
     * The health status reported when the health check is running.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $HEALTH_STATUS_RUNNING = 'running';

    /**
     * The health status reported when the container is healthy.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $HEALTH_STATUS_HEALTHY = 'healthy';

    /**
     * The health status reported when the container is unhealthy.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $HEALTH_STATUS_UNHEALTHY = 'unhealthy';

    /**
     * The name of the action without the detail.
     *
     * @var string
     */
    private $name;

    /**
     * The detail following the action name, or null if the action has no detail.
     *
     * @var null|string
     */
    private $detail;

    /**
     * @param string      $name   the name of the action
     * @param null|string $detail the detail following the action name
     * @psalm-param mixed $name
     * @psalm-param mixed $detail
     */
    public function __construct($name, $detail = null)
    {
        self::ensureNullableString($detail);

        if (!in_array($name, self::values(), true)) {
            throw new InvalidFormatException($name, self::values());
        }
        if ($detail !== null && !in_array($name, self::detailedValues(), true)) {
            throw new InvalidFormatException($name, self::detailedValues());
        }

        $this->name = $name;
        $this->detail = $detail;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Create a new instance of `EventAction` with the `CREATE` action.
     *
     * @return self
     */
    public static function CREATE()
    {
        return new self(static::$CREATE);
    }

    /**
     * Create a new instance of `EventAction` with the `START` action.
     *
     * @return self
     */
    public static function START()
    {
        return new self(static::$START);
    }

    /**
     * Create a new instance of `EventAction` with the `RESTART` action.
     *
     * @return self
     */
    public static function RESTART()
    {
        return new self(static::$RESTART);
    }

    /**
     * Create a new instance of `EventAction` with the `STOP` action.
     *
     * @return self
     */
    public static function STOP()
    {
        return new self(static::$STOP);
    }

    /**
     * Create a new instance of `EventAction` with the `CHECKPOINT` action.
     *
     * @return self
     */
    public static function CHECKPOINT()
    {
        return new self(static::$CHECKPOINT);
    }

    /**
     * Create a new instance of `EventAction` with the `PAUSE` action.
     *
     * @return self
     */
    public static function PAUSE()
    {
        return new self(static::$PAUSE);
    }

    /**
     * Create a new instance of `EventAction` with the `UNPAUSE` action.
     *
     * @return self
     */
    public static function UNPAUSE()
    {
        return new self(static::$UNPAUSE);
    }

    /**
     * Create a new instance of `EventAction` with the `ATTACH` action.
     *
     * @return self
     */
    public static function ATTACH()
    {
        return new self(static::$ATTACH);
    }

    /**
     * Create a new instance of `EventAction` with the `DETACH` action.
     *
     * @return self
     */
    public static function DETACH()
    {
        return new self(static::$DETACH);
    }

    /**
     * Create a new instance of `EventAction` with the `RESIZE` action.
     *
     * @return self
     */
    public static function RESIZE()
    {
        return new self(static::$RESIZE);
    }

    /**
     * Create a new instance of `EventAction` with the `UPDATE` action.
     *
     * @return self
     */
    public static function UPDATE()
    {
        return new self(static::$UPDATE);
    }

    /**
     * Create a new instance of `EventAction` with the `RENAME` action.
     *
     * @return self
     */
    public static function RENAME()
    {
        return new self(static::$RENAME);
    }

    /**
     * Create a new instance of `EventAction` with the `KILL` action.
     *
     * @return self
     */
    public static function KILL()
    {
        return new self(static::$KILL);
    }

    /**
     * Create a new instance of `EventAction` with the `DIE` action.
     *
     * The method is suffixed with an underscore because `die` is a reserved word that cannot be used
     * as a method name before PHP 7.0.
     *
     * @return self
     */
    public static function DIE_()
    {
        return new self(static::$DIE);
    }

    /**
     * Create a new instance of `EventAction` with the `OOM` action.
     *
     * @return self
     */
    public static function OOM()
    {
        return new self(static::$OOM);
    }

    /**
     * Create a new instance of `EventAction` with the `DESTROY` action.
     *
     * @return self
     */
    public static function DESTROY()
    {
        return new self(static::$DESTROY);
    }

    /**
     * Create a new instance of `EventAction` with the `REMOVE` action.
     *
     * @return self
     */
    public static function REMOVE()
    {
        return new self(static::$REMOVE);
    }

    /**
     * Create a new instance of `EventAction` with the `COMMIT` action.
     *
     * @return self
     */
    public static function COMMIT()
    {
        return new self(static::$COMMIT);
    }

    /**
     * Create a new instance of `EventAction` with the `TOP` action.
     *
     * @return self
     */
    public static function TOP()
    {
        return new self(static::$TOP);
    }

    /**
     * Create a new instance of `EventAction` with the `COPY` action.
     *
     * @return self
     */
    public static function COPY()
    {
        return new self(static::$COPY);
    }

    /**
     * Create a new instance of `EventAction` with the `ARCHIVE_PATH` action.
     *
     * @return self
     */
    public static function ARCHIVE_PATH()
    {
        return new self(static::$ARCHIVE_PATH);
    }

    /**
     * Create a new instance of `EventAction` with the `EXTRACT_TO_DIR` action.
     *
     * @return self
     */
    public static function EXTRACT_TO_DIR()
    {
        return new self(static::$EXTRACT_TO_DIR);
    }

    /**
     * Create a new instance of `EventAction` with the `EXPORT` action.
     *
     * @return self
     */
    public static function EXPORT()
    {
        return new self(static::$EXPORT);
    }

    /**
     * Create a new instance of `EventAction` with the `IMPORT` action.
     *
     * @return self
     */
    public static function IMPORT()
    {
        return new self(static::$IMPORT);
    }

    /**
     * Create a new instance of `EventAction` with the `SAVE` action.
     *
     * @return self
     */
    public static function SAVE()
    {
        return new self(static::$SAVE);
    }

    /**
     * Create a new instance of `EventAction` with the `LOAD` action.
     *
     * @return self
     */
    public static function LOAD()
    {
        return new self(static::$LOAD);
    }

    /**
     * Create a new instance of `EventAction` with the `TAG` action.
     *
     * @return self
     */
    public static function TAG()
    {
        return new self(static::$TAG);
    }

    /**
     * Create a new instance of `EventAction` with the `UNTAG` action.
     *
     * @return self
     */
    public static function UNTAG()
    {
        return new self(static::$UNTAG);
    }

    /**
     * Create a new instance of `EventAction` with the `PUSH` action.
     *
     * @return self
     */
    public static function PUSH()
    {
        return new self(static::$PUSH);
    }

    /**
     * Create a new instance of `EventAction` with the `PULL` action.
     *
     * @return self
     */
    public static function PULL()
    {
        return new self(static::$PULL);
    }

    /**
     * Create a new instance of `EventAction` with the `PRUNE` action.
     *
     * @return self
     */
    public static function PRUNE()
    {
        return new self(static::$PRUNE);
    }

    /**
     * Create a new instance of `EventAction` with the `DELETE` action.
     *
     * @return self
     */
    public static function DELETE()
    {
        return new self(static::$DELETE);
    }

    /**
     * Create a new instance of `EventAction` with the `ENABLE` action.
     *
     * @return self
     */
    public static function ENABLE()
    {
        return new self(static::$ENABLE);
    }

    /**
     * Create a new instance of `EventAction` with the `DISABLE` action.
     *
     * @return self
     */
    public static function DISABLE()
    {
        return new self(static::$DISABLE);
    }

    /**
     * Create a new instance of `EventAction` with the `CONNECT` action.
     *
     * @return self
     */
    public static function CONNECT()
    {
        return new self(static::$CONNECT);
    }

    /**
     * Create a new instance of `EventAction` with the `DISCONNECT` action.
     *
     * @return self
     */
    public static function DISCONNECT()
    {
        return new self(static::$DISCONNECT);
    }

    /**
     * Create a new instance of `EventAction` with the `RELOAD` action.
     *
     * @return self
     */
    public static function RELOAD()
    {
        return new self(static::$RELOAD);
    }

    /**
     * Create a new instance of `EventAction` with the `MOUNT` action.
     *
     * @return self
     */
    public static function MOUNT()
    {
        return new self(static::$MOUNT);
    }

    /**
     * Create a new instance of `EventAction` with the `UNMOUNT` action.
     *
     * @return self
     */
    public static function UNMOUNT()
    {
        return new self(static::$UNMOUNT);
    }

    /**
     * Create a new instance of `EventAction` with the `EXEC_CREATE` action.
     *
     * @param null|string $command the command following the action name
     * @psalm-param mixed $command
     *
     * @return self
     */
    public static function EXEC_CREATE($command = null)
    {
        return new self(static::$EXEC_CREATE, $command);
    }

    /**
     * Create a new instance of `EventAction` with the `EXEC_START` action.
     *
     * @param null|string $command the command following the action name
     * @psalm-param mixed $command
     *
     * @return self
     */
    public static function EXEC_START($command = null)
    {
        return new self(static::$EXEC_START, $command);
    }

    /**
     * Create a new instance of `EventAction` with the `EXEC_DIE` action.
     *
     * @return self
     */
    public static function EXEC_DIE()
    {
        return new self(static::$EXEC_DIE);
    }

    /**
     * Create a new instance of `EventAction` with the `EXEC_DETACH` action.
     *
     * @return self
     */
    public static function EXEC_DETACH()
    {
        return new self(static::$EXEC_DETACH);
    }

    /**
     * Create a new instance of `EventAction` with the `HEALTH_STATUS` action.
     *
     * @param null|string $status the status following the action name
     * @psalm-param mixed $status
     *
     * @return self
     */
    public static function HEALTH_STATUS($status = null)
    {
        return new self(static::$HEALTH_STATUS, $status);
    }

    /**
     * Create a new instance of `EventAction` with the `HEALTH_STATUS` action and the `running` status.
     *
     * @return self
     */
    public static function HEALTH_STATUS_RUNNING()
    {
        return new self(static::$HEALTH_STATUS, static::$HEALTH_STATUS_RUNNING);
    }

    /**
     * Create a new instance of `EventAction` with the `HEALTH_STATUS` action and the `healthy` status.
     *
     * @return self
     */
    public static function HEALTH_STATUS_HEALTHY()
    {
        return new self(static::$HEALTH_STATUS, static::$HEALTH_STATUS_HEALTHY);
    }

    /**
     * Create a new instance of `EventAction` with the `HEALTH_STATUS` action and the `unhealthy` status.
     *
     * @return self
     */
    public static function HEALTH_STATUS_UNHEALTHY()
    {
        return new self(static::$HEALTH_STATUS, static::$HEALTH_STATUS_UNHEALTHY);
    }

    /**
     * Create a new instance of `EventAction` from a string representation.
     *
     * @param string $action the string representation of the action (e.g. `die`, `health_status: healthy`)
     * @psalm-param mixed $action
     *
     * @throws InvalidFormatException if the provided action is not valid
     *
     * @return self
     */
    public static function fromString($action)
    {
        self::ensureString($action);

        if (in_array($action, self::values(), true)) {
            return new self($action);
        }
        $pos = strpos($action, ': ');
        if ($pos !== false) {
            $name = substr($action, 0, $pos);
            if (in_array($name, self::detailedValues(), true)) {
                return new self($name, substr($action, $pos + 2));
            }
        }

        throw new InvalidFormatException($action, self::values());
    }

    /**
     * Get all valid action names.
     *
     * @return string[]
     */
    public static function values()
    {
        return self::ensureStringList([
            static::$CREATE,
            static::$START,
            static::$RESTART,
            static::$STOP,
            static::$CHECKPOINT,
            static::$PAUSE,
            static::$UNPAUSE,
            static::$ATTACH,
            static::$DETACH,
            static::$RESIZE,
            static::$UPDATE,
            static::$RENAME,
            static::$KILL,
            static::$DIE,
            static::$OOM,
            static::$DESTROY,
            static::$REMOVE,
            static::$COMMIT,
            static::$TOP,
            static::$COPY,
            static::$ARCHIVE_PATH,
            static::$EXTRACT_TO_DIR,
            static::$EXPORT,
            static::$IMPORT,
            static::$SAVE,
            static::$LOAD,
            static::$TAG,
            static::$UNTAG,
            static::$PUSH,
            static::$PULL,
            static::$PRUNE,
            static::$DELETE,
            static::$ENABLE,
            static::$DISABLE,
            static::$CONNECT,
            static::$DISCONNECT,
            static::$RELOAD,
            static::$MOUNT,
            static::$UNMOUNT,
            static::$EXEC_CREATE,
            static::$EXEC_START,
            static::$EXEC_DIE,
            static::$EXEC_DETACH,
            static::$HEALTH_STATUS,
        ]);
    }

    /**
     * Get the action names that can be followed by a detail.
     *
     * @return string[]
     */
    public static function detailedValues()
    {
        return self::ensureStringList([
            static::$EXEC_CREATE,
            static::$EXEC_START,
            static::$HEALTH_STATUS,
        ]);
    }

    /**
     * Check if the action is `CREATE`.
     *
     * @return bool true if the action is `CREATE`, false otherwise
     */
    public function isCreate()
    {
        return $this->name === static::$CREATE;
    }

    /**
     * Check if the action is `START`.
     *
     * @return bool true if the action is `START`, false otherwise
     */
    public function isStart()
    {
        return $this->name === static::$START;
    }

    /**
     * Check if the action is `RESTART`.
     *
     * @return bool true if the action is `RESTART`, false otherwise
     */
    public function isRestart()
    {
        return $this->name === static::$RESTART;
    }

    /**
     * Check if the action is `STOP`.
     *
     * @return bool true if the action is `STOP`, false otherwise
     */
    public function isStop()
    {
        return $this->name === static::$STOP;
    }

    /**
     * Check if the action is `CHECKPOINT`.
     *
     * @return bool true if the action is `CHECKPOINT`, false otherwise
     */
    public function isCheckpoint()
    {
        return $this->name === static::$CHECKPOINT;
    }

    /**
     * Check if the action is `PAUSE`.
     *
     * @return bool true if the action is `PAUSE`, false otherwise
     */
    public function isPause()
    {
        return $this->name === static::$PAUSE;
    }

    /**
     * Check if the action is `UNPAUSE`.
     *
     * @return bool true if the action is `UNPAUSE`, false otherwise
     */
    public function isUnpause()
    {
        return $this->name === static::$UNPAUSE;
    }

    /**
     * Check if the action is `ATTACH`.
     *
     * @return bool true if the action is `ATTACH`, false otherwise
     */
    public function isAttach()
    {
        return $this->name === static::$ATTACH;
    }

    /**
     * Check if the action is `DETACH`.
     *
     * @return bool true if the action is `DETACH`, false otherwise
     */
    public function isDetach()
    {
        return $this->name === static::$DETACH;
    }

    /**
     * Check if the action is `RESIZE`.
     *
     * @return bool true if the action is `RESIZE`, false otherwise
     */
    public function isResize()
    {
        return $this->name === static::$RESIZE;
    }

    /**
     * Check if the action is `UPDATE`.
     *
     * @return bool true if the action is `UPDATE`, false otherwise
     */
    public function isUpdate()
    {
        return $this->name === static::$UPDATE;
    }

    /**
     * Check if the action is `RENAME`.
     *
     * @return bool true if the action is `RENAME`, false otherwise
     */
    public function isRename()
    {
        return $this->name === static::$RENAME;
    }

    /**
     * Check if the action is `KILL`.
     *
     * @return bool true if the action is `KILL`, false otherwise
     */
    public function isKill()
    {
        return $this->name === static::$KILL;
    }

    /**
     * Check if the action is `DIE`.
     *
     * @return bool true if the action is `DIE`, false otherwise
     */
    public function isDie()
    {
        return $this->name === static::$DIE;
    }

    /**
     * Check if the action is `OOM`.
     *
     * @return bool true if the action is `OOM`, false otherwise
     */
    public function isOom()
    {
        return $this->name === static::$OOM;
    }

    /**
     * Check if the action is `DESTROY`.
     *
     * @return bool true if the action is `DESTROY`, false otherwise
     */
    public function isDestroy()
    {
        return $this->name === static::$DESTROY;
    }

    /**
     * Check if the action is `REMOVE`.
     *
     * @return bool true if the action is `REMOVE`, false otherwise
     */
    public function isRemove()
    {
        return $this->name === static::$REMOVE;
    }

    /**
     * Check if the action is `COMMIT`.
     *
     * @return bool true if the action is `COMMIT`, false otherwise
     */
    public function isCommit()
    {
        return $this->name === static::$COMMIT;
    }

    /**
     * Check if the action is `TOP`.
     *
     * @return bool true if the action is `TOP`, false otherwise
     */
    public function isTop()
    {
        return $this->name === static::$TOP;
    }

    /**
     * Check if the action is `COPY`.
     *
     * @return bool true if the action is `COPY`, false otherwise
     */
    public function isCopy()
    {
        return $this->name === static::$COPY;
    }

    /**
     * Check if the action is `ARCHIVE_PATH`.
     *
     * @return bool true if the action is `ARCHIVE_PATH`, false otherwise
     */
    public function isArchivePath()
    {
        return $this->name === static::$ARCHIVE_PATH;
    }

    /**
     * Check if the action is `EXTRACT_TO_DIR`.
     *
     * @return bool true if the action is `EXTRACT_TO_DIR`, false otherwise
     */
    public function isExtractToDir()
    {
        return $this->name === static::$EXTRACT_TO_DIR;
    }

    /**
     * Check if the action is `EXPORT`.
     *
     * @return bool true if the action is `EXPORT`, false otherwise
     */
    public function isExport()
    {
        return $this->name === static::$EXPORT;
    }

    /**
     * Check if the action is `IMPORT`.
     *
     * @return bool true if the action is `IMPORT`, false otherwise
     */
    public function isImport()
    {
        return $this->name === static::$IMPORT;
    }

    /**
     * Check if the action is `SAVE`.
     *
     * @return bool true if the action is `SAVE`, false otherwise
     */
    public function isSave()
    {
        return $this->name === static::$SAVE;
    }

    /**
     * Check if the action is `LOAD`.
     *
     * @return bool true if the action is `LOAD`, false otherwise
     */
    public function isLoad()
    {
        return $this->name === static::$LOAD;
    }

    /**
     * Check if the action is `TAG`.
     *
     * @return bool true if the action is `TAG`, false otherwise
     */
    public function isTag()
    {
        return $this->name === static::$TAG;
    }

    /**
     * Check if the action is `UNTAG`.
     *
     * @return bool true if the action is `UNTAG`, false otherwise
     */
    public function isUntag()
    {
        return $this->name === static::$UNTAG;
    }

    /**
     * Check if the action is `PUSH`.
     *
     * @return bool true if the action is `PUSH`, false otherwise
     */
    public function isPush()
    {
        return $this->name === static::$PUSH;
    }

    /**
     * Check if the action is `PULL`.
     *
     * @return bool true if the action is `PULL`, false otherwise
     */
    public function isPull()
    {
        return $this->name === static::$PULL;
    }

    /**
     * Check if the action is `PRUNE`.
     *
     * @return bool true if the action is `PRUNE`, false otherwise
     */
    public function isPrune()
    {
        return $this->name === static::$PRUNE;
    }

    /**
     * Check if the action is `DELETE`.
     *
     * @return bool true if the action is `DELETE`, false otherwise
     */
    public function isDelete()
    {
        return $this->name === static::$DELETE;
    }

    /**
     * Check if the action is `ENABLE`.
     *
     * @return bool true if the action is `ENABLE`, false otherwise
     */
    public function isEnable()
    {
        return $this->name === static::$ENABLE;
    }

    /**
     * Check if the action is `DISABLE`.
     *
     * @return bool true if the action is `DISABLE`, false otherwise
     */
    public function isDisable()
    {
        return $this->name === static::$DISABLE;
    }

    /**
     * Check if the action is `CONNECT`.
     *
     * @return bool true if the action is `CONNECT`, false otherwise
     */
    public function isConnect()
    {
        return $this->name === static::$CONNECT;
    }

    /**
     * Check if the action is `DISCONNECT`.
     *
     * @return bool true if the action is `DISCONNECT`, false otherwise
     */
    public function isDisconnect()
    {
        return $this->name === static::$DISCONNECT;
    }

    /**
     * Check if the action is `RELOAD`.
     *
     * @return bool true if the action is `RELOAD`, false otherwise
     */
    public function isReload()
    {
        return $this->name === static::$RELOAD;
    }

    /**
     * Check if the action is `MOUNT`.
     *
     * @return bool true if the action is `MOUNT`, false otherwise
     */
    public function isMount()
    {
        return $this->name === static::$MOUNT;
    }

    /**
     * Check if the action is `UNMOUNT`.
     *
     * @return bool true if the action is `UNMOUNT`, false otherwise
     */
    public function isUnmount()
    {
        return $this->name === static::$UNMOUNT;
    }

    /**
     * Check if the action is `EXEC_CREATE`.
     *
     * @return bool true if the action is `EXEC_CREATE`, false otherwise
     */
    public function isExecCreate()
    {
        return $this->name === static::$EXEC_CREATE;
    }

    /**
     * Check if the action is `EXEC_START`.
     *
     * @return bool true if the action is `EXEC_START`, false otherwise
     */
    public function isExecStart()
    {
        return $this->name === static::$EXEC_START;
    }

    /**
     * Check if the action is `EXEC_DIE`.
     *
     * @return bool true if the action is `EXEC_DIE`, false otherwise
     */
    public function isExecDie()
    {
        return $this->name === static::$EXEC_DIE;
    }

    /**
     * Check if the action is `EXEC_DETACH`.
     *
     * @return bool true if the action is `EXEC_DETACH`, false otherwise
     */
    public function isExecDetach()
    {
        return $this->name === static::$EXEC_DETACH;
    }

    /**
     * Check if the action is `HEALTH_STATUS`.
     *
     * @return bool true if the action is `HEALTH_STATUS`, false otherwise
     */
    public function isHealthStatus()
    {
        return $this->name === static::$HEALTH_STATUS;
    }

    /**
     * Check if the action is `HEALTH_STATUS` with the `running` status.
     *
     * @return bool true if the action is `HEALTH_STATUS` with the `running` status, false otherwise
     */
    public function isHealthStatusRunning()
    {
        return $this->name === static::$HEALTH_STATUS && $this->detail === static::$HEALTH_STATUS_RUNNING;
    }

    /**
     * Check if the action is `HEALTH_STATUS` with the `healthy` status.
     *
     * @return bool true if the action is `HEALTH_STATUS` with the `healthy` status, false otherwise
     */
    public function isHealthStatusHealthy()
    {
        return $this->name === static::$HEALTH_STATUS && $this->detail === static::$HEALTH_STATUS_HEALTHY;
    }

    /**
     * Check if the action is `HEALTH_STATUS` with the `unhealthy` status.
     *
     * @return bool true if the action is `HEALTH_STATUS` with the `unhealthy` status, false otherwise
     */
    public function isHealthStatusUnhealthy()
    {
        return $this->name === static::$HEALTH_STATUS && $this->detail === static::$HEALTH_STATUS_UNHEALTHY;
    }

    /**
     * Get the name of the action without the detail.
     *
     * @return string the name of the action
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the detail following the action name.
     *
     * @return null|string the detail, or null if the action has no detail
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Get the string representation of the action.
     *
     * @return string the string representation of the action, including the detail if any
     */
    public function toString()
    {
        if ($this->detail === null) {
            return $this->name;
        }

        return $this->name.': '.$this->detail;
    }
}
