#!/usr/bin/python3
# This script connects to AWS Titan and gets an image generated

import os
import connix
import run
import boto3
import json
import random
import base64

print(connix.header()	)
form = connix.form()

if 'prompt' not in form:
	run.error("Missing field.")

prompt = form['prompt']

bedrock = boto3.client("bedrock-runtime", region_name=run.config('AWS_REGION'))

body = json.dumps({
	"taskType": "TEXT_IMAGE",
	"textToImageParams": {
		"text": prompt
	},
	"imageGenerationConfig": {
		"numberOfImages": 1,
		"quality": "premium",
		"width": 1024,
		"height": 1024,
		"cfgScale": 7.5,
		"seed": random.randint(1, 214783646)
	}
})

response = bedrock.invoke_model(
	body = body,
	modelId = "amazon.titan-image-generator-v1",
	accept = "application/json",
	contentType = "application/json"
)
output = json.loads(response.get('body').read())['images'][0]

print(output)
