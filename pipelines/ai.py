#!/usr/bin/python3
# This script connects to OpenAI and sends a prompt + context to the GPT-3 engine.

import os
import openai
import connix
import run
import boto3
import json
import time

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

if 'prompt' not in form:
	run.error("Missing field.")

prompt = form['prompt']
context = ""
if 'context' in form:
	context = form['context']
model = "chatgpt"
if 'model' in form:
	model = form['model']

def titan(context, prompt):
	bedrock = boto3.client("bedrock-runtime", region_name=run.config("AWS_REGION"))
	body = json.dumps({
		"inputText": context + "\n\n" + prompt,
		"textGenerationConfig": {
			"maxTokenCount": 512,
			"stopSequences": [],
			"temperature": 0,
			"topP": 0.9
		}
	})
	response = bedrock.invoke_model(
		body = body,
		modelId = "amazon.titan-text-express-v1",
		accept = "application/json",
		contentType = "application/json"
	)
	raw = json.loads(response.get('body').read())
	output = raw.get('results')[0].get('outputText').replace('\n','<br>').lstrip()
	return output

def chatgpt(context, prompt):
	completion = openai.Completion.create(engine=run.config('OPENAI_MODEL'), prompt="context:" + context + "\n\n" + "prompt:" + prompt, max_tokens=1000, top_p=1, temperature=0.7, frequency_penalty=0, presence_penalty=0)
	output = completion.choices[0].text
	return output

if model == "chatgpt":
	openai.api_key = run.config('OPENAI_KEY')
	print(chatgpt(context, prompt))
elif model == "titan":
	print(titan(context, prompt))
else:
	run.error("Unknown model.")
