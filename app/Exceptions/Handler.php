<?php

namespace App\Exceptions;


use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*') || $request->is('admin/*')){
            //判断如果是提交数据验证错误
            if ($exception instanceof ValidationException){
                //$this->error 是自己封装的一个 Trait 返回 json 数据，您也可以自己封装，这里不再展示
                return $this->error(current($exception->errors())[0],42200,$exception->status);
                //判断如果是鉴权错误
            }elseif($exception instanceof AuthenticationException){
                //$this->error 是自己封装的一个 Trait 返回 json 数据，您也可以自己封装，这里不再展示
                return $this->error('',$exception);
                return $this->error('授权失败',[],401);
            }
        }
        return parent::render($request, $exception);
    }
}
