<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Monolog\Logger;

class JobsSendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	/**
	运行队列：
	php artisan queue:work database --daemon --queue=high,order_add,register,default,low --tries=3 --sleep=3 --timeout=85
	本地测试：php artisan queue:work database --queue=high,order_add,register,default,low --tries=3 --sleep=3 --timeout=85
	【队列持续挂着执行： nohup php artisan queue:work database --daemon --queue=high,order_add,register,default,low --tries=3 --sleep=3 --timeout=85 &】
	重启队列：【如果代码改变而队列处理器没有重启，他们是不能应用新代码的。所以最简单的方式就是重新部署过程中要重启队列处理器。你可以很优雅地只输入 queue:restart 来重启所有队列处理器】
	php artisan queue:restart
	*/
	
	/**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 85;//(秒)。在任务类中定义一个变量来设置可运行的最大描述，如果在类和命令行中都定义了最大尝试次数， Laravel 会优先执行任务类中的值.
    //{note} --timeout 应该永远都要比 retry_after 短至少几秒钟的时间。这样就能保证任务进程总能在失败重试前就被杀死了。如果你的 --timeout 选项大于 retry_after 配置选项，你的任务可能被执行两次。
	
	/**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 3;
    
	protected $to_email;//接收者邮箱
	protected $to_name;//接收者名称
	protected $subject;//邮件主题
	protected $send_arr;//邮件参数
	protected $view_url;//邮件模板路径

    /**
     * Create a new job instance.创建一个新的任务实例。(构造函数 可选，用来传参)
     *
     * @return void
     */
    public function __construct($to_email,$to_name,$subject,$send_arr,$view_url)
    {
        $this->onConnection('mails');
        //
        $this->to_email 	= $to_email;//接收者邮箱
        $this->to_name 		= $to_name;//接收者名称
        $this->subject 		= $subject;//邮件主题
        $this->send_arr 	= $send_arr;//邮件参数
        $this->view_url 	= $view_url;//邮件模板路径
    }

    /**
     * Execute the job.(必选，实现队列任务逻辑)
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        //
        try {
        	$to_email 	= $this->to_email;//接收者邮箱
	        $to_name 	= $this->to_name;//接收者名称
	        $subject 	= $this->subject;//邮件主题
	        $send_arr 	= $this->send_arr;//邮件参数
	        $view_url 	= $this->view_url;//邮件模板路径.例如：'email.order.order_add'
	        $send_email = Mail::send($view_url, $send_arr , function ($m) use ($to_email, $to_name, $subject) {
	            $m->to($to_email, $to_name)->subject($subject);
	        });
	    } catch(\Swift_TransportException $e) {
            //日志记录发送失败--------------------------
            addLog('邮件发送失败', [
    	        'exception' => $e->getMessage()
            ], 'emails', Logger::ERROR);
	    }
    }

    /**
     * 要处理的失败任务。(可选，当任务失败时执行)
     *
     * @param  \Exception $exception
     *
     * @return void
     * @throws \Exception
     */
    public function failed(\Exception $exception)
    {
        // 给用户发送失败通知，等等...
        // 给开发人员发送告警邮件
        addLog('邮件发送失败', [
            'exception' => $exception->getMessage()
        ], 'emails', Logger::ERROR);
    }
}
