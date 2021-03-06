<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionCreateRequest;
use App\Question;
use App\Tag;
use Illuminate\Http\Request;
use Auth;

class QuestionController extends Controller
{
    private $question;
    private $tag;
    function __construct(Question $question, Tag $tag)
    {
        $this->question = $question;
        $this->tag = $tag;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = $this->question->with('votes')->all();
        return view('home', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = $this->tag->all();
        return view('questions.question-create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionCreateRequest $request)
    {
        $this->question->title = $request->get('title');
        $this->question->content = $request->get('content');
        $this->question->user_id = Auth::id();
        $this->question->slug = str_slug($request->get('title'));

        $this->question->save();
        $this->question->tags()->sync($request->tags, false);
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        return view('questions.question-show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }
}
