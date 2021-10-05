#wrong token
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKE"
read -p "Press Enter to resume ..."