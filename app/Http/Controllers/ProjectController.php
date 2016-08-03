<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\GitHubHelper;
use Auth;
use App\Models\Project;
use App\Models\ProjectType;

class ProjectController extends Controller
{
   	public function create(Request $request)
   	{
         $type = $request->input("type", null);

         

         $name = null;
         $language = "java";
         $url = null;
         $project_type_id = null;

         if($type == "empty")
         {
            $name = $request->input("projectName", null);
            if($name == null || $name == "null" || $name == "")
            {
               return response()->json(["success" => false, "message" => "You must choose a Project Name"]);
            }
            $project_type_id = ProjectType::where("name", "=", "empty")->first()->id;

         }else if($type == "github")
         {          
            $name = $request->input("repoName", null);
            $language = $request->input("language", null);

            if($name == null || $name == "null" || $name == "")
            {
               return response()->json(["success" => false, "message" => "You must choose a Repository"]);
            }

            if($language == null || $language == "null" || $language == "")
            {
               return response()->json(["success" => false, "message" => "You must choose a Language"]);
            }

            $project_type_id = ProjectType::where("name", "=", "github")->first()->id;
         }else
         {
            return response()->json(["success" => false, "message" => "You must choose a Project Type"]);
         }

         $project = Project::where("name", $name)->where("user_id", Auth::user()->id)->firstOrCreate([
            "name" => $name,
            "language" => $language,
            "user_id" => Auth::user()->id,
            "project_type_id" => $project_type_id
         ]);

   		return response()->json(["success" => true]);
   	}

      public function get(Request $request, $project = null){
         $data = ["success" => true];
         $projects = null;
         if($project != null)
         {
            $data["projects"] = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->get();
         }else{
            $data["projects"] = Project::where("user_id", "=", Auth::user()->id)->get();
         }

         return response()->json($data);
      }
}
