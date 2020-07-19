<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check()) { // 認証済みの場合
        
            // 認証済みユーザを取得
            $user = \Auth::user();

            
            // タスク一覧を取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            return view('tasks.index', [
                'user' => $user,
                'tasks' => $tasks,
            ]);
        }else{
            return view('welcome');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     
      // バリデーション
        $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        // メッセージを作成
        $task = new Task;
        $task->user_id = \Auth::id();
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
// 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を表示
        if (\Auth::id() === $task->user_id) {
        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);}else{
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
         // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
// 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、編集を表示
        if (\Auth::id() === $task->user_id) {
        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);}else{
            return redirect('/');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        // バリデーション
        $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        
         // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        // メッセージを更新
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        // idの値でメッセージを検索して取得
        $task = \App\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
