#!/bin/bash

# Source the .env file to get the MODEL variable
# Try multiple possible locations for the .env file
if [ -f ".env" ]; then
    echo "Found .env in current directory"
    source ".env"
else
    echo "Error: .env file not found after trying multiple locations!"
    echo "Please mount the .env file into the container or specify the correct path."
    exit 1
fi

# Check if MODEL is one of the locally hosted models
if [[ "$MODEL" == "Llama3.2" || "$MODEL" == "Mixtral" || "$MODEL" == "Bielik" || "$MODEL" == "DeepSeek-R1-7B" || "$MODEL" == "DeepSeek-Coder-v2" ]]; then
    # Start Ollama in the background
    /bin/ollama serve &
    # Record Process ID
    pid=$!

    # Pause for Ollama to start
    sleep 5

    # Pull only the selected model
    case "$MODEL" in
        "Llama3.2")
            echo "🔴 Retrieving LLAMA3 model..."
            ollama pull llama3.2
            echo "🟢 Done!"
            ;;
        "Mixtral")
            echo "🔴 Retrieving Mixtral model..."
            ollama pull mistral
            echo "🟢 Done!"
            ;;
        "Bielik")
            echo "🔴 Retrieving Bielik model..."
            ollama pull mwiewior/bielik
            echo "🟢 Done!"
            ;;
        "DeepSeek-R1-7B")
            echo "🔴 Retrieving DeepSeek-R1:7B model..."
            ollama pull deepseek-r1:7b
            echo "🟢 Done!"
            ;;
        "DeepSeek-Coder-v2")
            echo "🔴 Retrieving DeepSeek-Coder-v2 model..."
            ollama pull deepseek-coder-v2
            echo "🟢 Done!"
            ;;
    esac

    # For embedding capabilities, always pull the mxbai model
    echo "🔴 Retrieving mxbai embedding model..."
    ollama pull mxbai-embed-large
    echo "🟢 Done!"

    # Wait for Ollama process to finish
    wait $pid
else
    echo "Using cloud-based model: $MODEL"
    echo "No need to pull local models via Ollama."
    exit 0
fi