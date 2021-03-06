<?php

namespace ArtisanSDK\Server\Contracts;

use ArtisanSDK\Server\Entities\Commands;
use ArtisanSDK\Server\Entities\Connections;
use ArtisanSDK\Server\Entities\Listeners;
use ArtisanSDK\Server\Entities\Processes;
use ArtisanSDK\Server\Entities\Timers;
use ArtisanSDK\Server\Entities\Topics;
use Exception;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue;

interface Manager
{
    /**
     * Setup the initial state of the manager when starting.
     *
     * @return self
     */
    public function boot();

    /**
     * Called when the server is started.
     *
     * @return self
     */
    public function start();

    /**
     * Called when the server is stopped.
     *
     * @return self
     */
    public function stop();

    /**
     * Called when a new connection is opened.
     *
     * @param \ArtisanSDK\Server\Contracts\Connection $connection being opened
     *
     * @return self
     */
    public function open(Connection $connection);

    /**
     * Send message to one connection.
     *
     * @param \ArtisanSDK\Server\Contracts\Message    $message    to send
     * @param \ArtisanSDK\Server\Contracts\Connection $connection to send to
     *
     * @return self
     */
    public function send(Message $message, Connection $connection);

    /**
     * Send message to one connection and then close the connection.
     *
     * @param \ArtisanSDK\Server\Contracts\Message    $message    to send
     * @param \ArtisanSDK\Server\Contracts\Connection $connection to send to
     *
     * @return self
     */
    public function end(Message $message, Connection $connection);

    /**
     * Broadcast message to multiple connections.
     *
     * @param \ArtisanSDK\Server\Contracts\Message    $message
     * @param \ArtisanSDK\Server\Entities\Connections $connections to send to (defaults to everyone)
     *
     * @return self
     */
    public function broadcast(Message $message, Connections $connections = null);

    /**
     * Called when a new message is received from an open connection.
     *
     * @param \ArtisanSDK\Server\Contracts\Message    $message    payload received
     * @param \ArtisanSDK\Server\Contracts\Connection $connection sending the message
     *
     * @return self
     */
    public function receive(Message $message, Connection $connection);

    /**
     * Called when an open connection is closed.
     *
     * @param \ArtisanSDK\Server\Contracts\Connection $connection to be closed
     *
     * @return self
     */
    public function close(Connection $connection);

    /**
     * Called when an error occurs on the connection.
     *
     * @param \ArtisanSDK\Server\Contracts\Connection $connection that errored
     * @param \Exception                              $exception  caught
     *
     * @return self
     */
    public function error(Connection $connection, Exception $exception);

    /**
     * Get or set the connections on the server.
     *
     * @example connections() ==> \ArtisanSDK\Server\Entities\Connections
     *          connections($connections) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Connections $connections
     *
     * @return \ArtisanSDK\Server\Entities\Connections|self
     */
    public function connections(Connections $connections = null);

    /**
     * Get or set the topics available for subscribing.
     *
     * @example topics() ==> \ArtisanSDK\Server\Entities\Topics
     *          topics($topics) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Topics $topics
     *
     * @return \ArtisanSDK\Server\Entities\Topics|self
     */
    public function topics(Topics $topics = null);

    /**
     * Register a new topic in the collection of topics.
     *
     * @param \ArtisanSDK\Server\Contracts\Topic $topic to register
     *
     * @return self
     */
    public function register(Topic $topic);

    /**
     * Unregister an existing topic from the collection of topics.
     *
     * @param \ArtisanSDK\Server\Contracts\Topic $topic to unregister
     *
     * @return self
     */
    public function unregister(Topic $topic);

    /**
     * Subscribe a connection to the topic.
     *
     * @param \ArtisanSDK\Server\Contracts\Topic      $topic      to subscribe to
     * @param \ArtisanSDK\Server\Contracts\Connection $connection to subscribe to topic
     *
     * @return self
     */
    public function subscribe(Topic $topic, Connection $connection);

    /**
     * Unsubscribe a connection from the topic.
     *
     * @param \ArtisanSDK\Server\Contracts\Topic      $topic      to unsubscribe from
     * @param \ArtisanSDK\Server\Contracts\Connection $connection to unsubscribe from topic
     *
     * @return self
     */
    public function unsubscribe(Topic $topic, Connection $connection);

    /**
     * Get or set the timers available for executing.
     *
     * @example timers() ==> \ArtisanSDK\Server\Entities\Timers
     *          timers($timers) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Timers $timers
     *
     * @return \ArtisanSDK\Server\Entities\Timers|self
     */
    public function timers(Timers $timers = null);

    /**
     * Add a timer to the event loop.
     *
     * @param \ArtisanSDK\Server\Contracts\Timer $timer to add
     *
     * @return self
     */
    public function add(Timer $timer);

    /**
     * Pause a timer in the event loop so that it does not run until resumed.
     *
     * @param \ArtisanSDK\Server\Contracts\Timer $timer to pause
     *
     * @return self
     */
    public function pause(Timer $timer);

    /**
     * Resume a timer in the event loop that was previously paused.
     *
     * @param \ArtisanSDK\Server\Contracts\Timer $timer to resume
     *
     * @return self
     */
    public function resume(Timer $timer);

    /**
     * Add a timer that runs only once after the initial delay.
     *
     * @param \ArtisanSDK\Server\Contracts\Timer $timer to run once
     *
     * @return self
     */
    public function once(Timer $timer);

    /**
     * Cancel a timer in the event loop that is currently active.
     *
     * @param \ArtisanSDK\Server\Contracts\Timer $timer to cancel
     *
     * @return self
     */
    public function cancel(Timer $timer);

    /**
     * Get the event loop the server runs on.
     *
     * @return \React\EventLoop\LoopInterface
     */
    public function loop();

    /**
     * Get or set the queue connector the server uses.
     *
     * @example connector() ==> \Illuminate\Contracts\Queue\Queue
     *          connector($connector) ==> self
     *
     * @param \Illuminate\Contracts\Queue\Queue $instance
     *
     * @return \Illuminate\Contracts\Queue\Queue|self
     */
    public function connector(Queue $instance = null);

    /**
     * Get or set the queue the server processes.
     *
     * @example queue() ==> 'server'
     *          queue('server') ==> self
     *
     * @param string $name of queue
     *
     * @return string|self
     */
    public function queue($name = null);

    /**
     * Process a job that has been popped off the queue.
     *
     * @param \Illuminate\Contracts\Queue\Job $job to be processed
     *
     * @return self
     */
    public function work(Job $job);

    /**
     * Get or set the commands available to be ran.
     *
     * @example commands() ==> \ArtisanSDK\Server\Entities\Commands
     *          commands($commands) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Commands $commands
     *
     * @return \ArtisanSDK\Server\Entities\Commands|self
     */
    public function commands(Commands $commands = null);

    /**
     * Run a command immediately within this tick of the event loop.
     *
     * @param \ArtisanSDK\Server\Contracts\Command $command to run
     *
     * @return self
     */
    public function run(Command $command);

    /**
     * Run a command in the next tick of the event loop.
     *
     * @param \ArtisanSDK\Server\Contracts\Command $command to run
     *
     * @return self
     */
    public function next(Command $command);

    /**
     * Abort a command before it has a chance to run.
     *
     * @param \ArtisanSDK\Server\Contracts\Command $command to abort
     *
     * @return self
     */
    public function abort(Command $command);

    /**
     * Delay the execution of a command.
     *
     * @param \ArtisanSDK\Server\Contracts\Command $command to delay
     * @param int                                  $delay   in milliseconds
     *
     * @return self
     */
    public function delay(Command $command, $delay);

    /**
     * Get or set the listeners that are registered.
     *
     * @example listeners() ==> \ArtisanSDK\Server\Entities\Listeners
     *          listeners($listeners) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Listeners $listeners
     *
     * @return \ArtisanSDK\Server\Entities\Listeners|self
     */
    public function listeners(Listeners $listeners = null);

    /**
     * Bind a message to a command so that the command listens for
     * the message as an event and is ran when the event occurs.
     *
     * @param \ArtisanSDK\Server\Contracts\Message $message to listen for
     * @param \ArtisanSDK\Server\Contracts\Command $command to run
     *
     * @return self
     */
    public function listen(Message $message, Command $command);

    /**
     * Add a listener to the collection of listeners.
     *
     * @param \ArtisanSDK\Server\Contracts\Listener $listener to add
     *
     * @return self
     */
    public function listener(Listener $listener);

    /**
     * Remove a listener from the collection of listeners.
     *
     * @param \ArtisanSDK\Server\Contracts\Listener $listener to remove
     *
     * @return self
     */
    public function silence(Listener $listener);

    /**
     * Get or set the processes that are running.
     *
     * @example processes() ==> \ArtisanSDK\Server\Entities\Processes
     *          processes($processes) ==> self
     *
     * @param \ArtisanSDK\Server\Entities\Processes $processes
     *
     * @return \ArtisanSDK\Server\Entities\Processes|self
     */
    public function processes(Processes $processes = null);

    /**
     * Add a process to the processes and begin running it.
     *
     * @param \ArtisanSDK\Server\Contracts\Process $process to add
     *
     * @return self
     */
    public function execute(Process $process);

    /**
     * Stop a process that is running and remove it from the processes.
     *
     * @param \ArtisanSDK\Server\Contracts\Process $process to terminate
     *
     * @return self
     */
    public function terminate(Process $process);

    /**
     * Pipe the output of one process to the input of another process.
     * Both processes will be added to the processes and started automatically.
     *
     * @param \ArtisanSDK\Server\Contracts\Process $input  to pipe to output
     * @param \ArtisanSDK\Server\Contracts\Process $output to receive from input pipe
     *
     * @return self
     */
    public function pipe(Process $input, Process $output);

    /**
     * Run a deferred promise (for resolving asynchronous code).
     *
     * @param \ArtisanSDK\Server\Contracts\Promise $promise to defer
     * @param mixed                                $result  to resolve promise for
     *
     * @return self
     */
    public function promise(Promise $promise, $result = null);
}
