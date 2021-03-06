<?php

namespace App\Http\Controllers;

use App\Question;
use App\User;
use App\Vote;
use Illuminate\Http\Request;
use Auth;

class VoteController extends Controller
{
    private $vote;
    private $user;
    private $question;

    function __construct(Vote $vote, User $user, Question $question)
    {
        $this->vote = $vote;
        $this->user = $user;
        $this->question = $question;
    }

    public function upvote(Request $request)
    {
        $v = $this->vote::where('question_id', $request->question_id)
            ->where('user_id', Auth::id())->first();
        $q = $this->question::find($request->question_id);
        $q->vote += 1;
        $q->save();
        $user = $this->user::find($request->whom_question);
        $profile = $user->profile;
        $profile->reputation += 10;
        $profile->save();
        if ($v) {
            $v->upvote = true;
            $v->downvote = false;
            $v->user_id = Auth::id();
            $v->question_id = $request->question_id;
            $v->save();
        } else {
            $this->vote->user_id = Auth::id();
            $this->vote->upvote = true;
            $this->vote->question_id = $request->question_id;

            $this->vote->save();
        }
        return response()->json(['status' => $request->question_id]);
    }

    public function downvote(Request $request)
    {
        $v = $this->vote::where('question_id', $request->question_id)
            ->where('user_id', Auth::id())->first();

        $q = $this->question::find($request->question_id);
        $q->vote -= 1;
        $q->save();

        $user = $this->user::find($request->whom_question);
        $profile = $user->profile;
        $profile->reputation -= 10;
        $profile->save();
        if ($v) {
            $v->upvote = false;
            $v->downvote = true;
            $v->user_id = Auth::id();
            $v->question_id = $request->question_id;
            $v->save();
        } else {
            $this->vote->user_id = Auth::id();
            $this->vote->downvote = true;
            $this->vote->question_id = $request->question_id;

            $this->vote->save();
        }
        return response()->json(['status' => $request->question_id]);
    }

    public function favorite(Request $request)
    {
        $v = $this->vote::where('question_id', $request->question_id)
            ->where('user_id', Auth::id())->first();
        if ($v) {
            $v->favorite = true;

            $v->save();
        } else {
            $this->vote->user_id = Auth::id();
            $this->vote->favorite = true;
            $this->vote->question_id = $request->question_id;

            $this->vote->save();
        }
        return response()->json(['status' => 'success'], 200);
    }

    public function unfavorite(Request $request)
    {
        $v = $this->vote::where('question_id', $request->question_id)
            ->where('user_id', Auth::id())->first();
        if ($v) {
            $v->favorite = false;

            $v->save();
        }
        return response()->json(['status' => 'success'], 200);
    }

    public function check_favorite($question_id)
    {
        $v = $this->vote::where('question_id', $question_id)
            ->where('user_id', Auth::id())->first();

        if ($v) {
            if ($v->user_id == Auth::id() && $v->favorite == false) {
                return response()->json(['status' => 'ok', 'favorite' => false]);
            } else if ($v->user_id == Auth::id() && $v->favorite == true) {
                return response()->json(['status' => 'ok', 'favorite' => true]);
            }
        }

        return response()->json(['status' => 'failed']);
    }

    public function check_vote($question_id)
    {
        $v = $this->vote::where('question_id', $question_id)
            ->where('user_id', Auth::id())->first();

        if ($v) {
            if ($v->upvote == true && $v->downvote == false) {
                return response()->json(['status' => 'ok', 'upvote' => true, 'downvote' => false]);
            } else if ($v->upvote == false && $v->downvote == true) {
                return response()->json(['status' => 'ok', 'upvote' => false, 'downvote' => true]);
            } else if ($v->upvote == false && $v->downvote == false) {
                return response()->json(['status' => 'ok', 'upvote' => false, 'downvote' => false]);
            }
        }

        return response()->json(['status' => 'failed']);
    }
}
