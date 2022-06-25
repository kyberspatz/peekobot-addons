/*
Thx, Peekobot! 
https://github.com/Peekobot/peekobot
https://twitter.com/magicroundabout/
	
My wife loves to solve quizzes. So I wrote an addon for the Peekobot so that the Peekobot can ask a quiz.

Usage: 
• Include the script with <script src="quiz_en.js"></script> in the index.html, before peekobot.js.
• Connect the quiz in conversations.js with the node "quiz_intro".
• fill out "your_exit_node" (see the variable below). Which this node you can connect the exiting of the script with your current conversation flow (for example to go back to the main menu)
• Look at the sample questions. This way you can add as many questions as you like.

The questions are shuffled, the answers are shuffled. A highscore is counted (by setting a cookie). At the end there is the possibility to display the highscore.

Have fun with the script!

License of the script: The Unlicense (unlicense.org)

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>

*/

var your_exit_node = "the_name_of_your_exit_node" // place your exit node here

let questions = [
  {
	  Q: "What was the first mass produced car model?",
	  "❌1": "Mercedes-Benz 300SL Gullwing",
	  "❌2": "Citroen DS",
	  "❌3": "VW Golf",
	  "✔": "Ford Model T",
  },
    {
	  Q: "Which UK TV channel started broadcasting in 1964?",
	  "❌1": "Channel 4",
	  "❌2": "MTV",
	  "❌3": "BBC One",
	  "✔": "BBC2",
  },

];

    chat.quiz_intro = {
        text: 'Cool, let&apos;s begin 😃 '+startquiz(),
		next: 'quiz1'
	}

var number = 0;
  
function startquiz()
{
	localStorage.setItem("score", 0);
	return("Ok, let's start the quiz. "+questions.length+" questions are to be answered.");
}

function score_plus()
{
	points = localStorage.getItem("score");
	points = parseInt(points);
	points++;
	localStorage.setItem("score",points);
	points = localStorage.getItem("score");	
}

function score_minus()
{
	punkte = localStorage.getItem("score");
	punkte = parseInt(punkte);
	//punkte--;
	//localStorage.setItem("score",punkte);
	punkte = localStorage.getItem("score");
}

function shufflequestion_array(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

shufflequestion_array(questions);
questions.forEach(unravel,number);

function answer_is_wrong()
{
	return "I'm sorry, your answer is wrong.";
}

function answer_is_correct()
{
	return "Yes, your answer is correct.";
}

function unravel(item,number){
	
	number++;
		
	var question1 = 
			{
                text: '<span style="cursor: help;padding:0.3em;" onclick="score_minus()">'+item["❌1"]+'</span>',
                next: 'quiz'+number+'_wrong'
            }
		var question2 = 
			{
                text: '<span style="cursor: help;padding:0.3em;" onclick="score_minus()">'+item["❌2"]+'</span>',
                next: 'quiz'+number+'_wrong'
            }
		var question3 = 
			{
                text: '<span style="cursor: help;padding:0.3em;" onclick="score_minus()">'+item["❌3"]+'</span>',
                next: 'quiz'+number+'_wrong'
            }
		var question4 = 
			{
              text: '<span style="cursor: help;padding:0.3em;" onclick="score_plus()">'+item["✔"]+'</span>',
                next: 'quiz'+number+'_correct'
            }				
			
		
question_array = [question1,question2,question3,question4]
shufflequestion_array(question_array);

	chat["quiz"+number] = {   text: 'Question '+number+' of '+questions.length+':<br>'+item["Q"],
        options: [
            question_array[0],question_array[1],question_array[2],question_array[3], { text: '<span style="color:rgba(0,0,0,0.4)">(abort Quiz)</span>', next: 'quiz_exit' } 
		]
	}
	
	if(number<questions.length){
		
	chat["quiz"+number+"_wrong"] = { text: answer_is_wrong(), next: 'quiz'+(number+1) } 
	chat["quiz"+number+"_correct"] = { text: answer_is_correct(), next: 'quiz'+(number+1)  } 

	}
	else 
	{
		chat["quiz"+number+"_wrong"] = { text: answer_is_wrong(), next: 'quiz_exit_pre' } 
	chat["quiz"+number+"_correct"] = { text: answer_is_correct(), next: 'quiz_exit_pre'  } 
	}
	
}

chat.quiz_exit_pre = {
        text: 'This was a lot of fun 😃 Thank you so much 😊',
		next: 'quiz_exit'
		 
    }
	
function highscore_result()
{
	points = localStorage.getItem("score");
	points = parseInt(points);
	document.getElementById("highscore").innerHTML = "Highscore: "+points+"<br>"+"Questions: "+questions.length;
}	

chat.quiz_exit = {
        text: '<p>Thank you for taking the quiz 😎 Soon there will be more questions added 😇</p><div id="highscore"><p>Do you like to see your highscore?</p><p><button onclick="highscore_result()">click here</button></p>',
		next: your_exit_node
    }	






	
	
