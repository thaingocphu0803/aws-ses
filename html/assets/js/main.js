class SendMail {
    constructor(){
        this.responseMessage = document.getElementById('response_message');
		this.from = document.getElementById('from');
        this.to = document.getElementById('to');
        this.subject = document.getElementById('subject');
        this.message = document.getElementById('message');
        this.submitBtn = document.getElementById('summit_btn');
		this.myForm = document.getElementById('my-form');

        this.initEvent();
    }

    initEvent(){
        // Tự động điều chỉnh kích thước của textarea khi nhập nội dung
        this.message.addEventListener('input', function() {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        });	

        // Sự kiện gửi mail, sử dụng arrow function để giữ ngữ cảnh "this" của lớp
        this.submitBtn.addEventListener('click',async () => {
           this.sendmail();
        });
    }

    validation(){
        if(!this.from.value){
			this.responseMessage.hidden = false;
			this.responseMessage.textContent = 'The from field is required.'
			this.responseMessage.classList.remove('success');
			this.responseMessage.classList.add('error');
			return false;
		}else if(!this.to.value){
			this.responseMessage.hidden = false;
			this.responseMessage.textContent = 'The to field is required.'
			this.responseMessage.classList.remove('success');
			this.responseMessage.classList.add('error');
			return false;
		}

		return true;
    }

	async sendmail(){
		if(!this.validation()) return;

		let payload = {
			from: this.from.value,
			to: [this.to.value],
			cc:[],
			bcc:[],
			subject:this.subject.value,
			body: this.message.value
		}

		let response  = await this.sendRequest(payload);
		
		this.handleResponse(response);
	}

	handleResponse(response)
	{
		if(response.success){
			this.responseMessage.hidden = false;
			this.responseMessage.textContent = response.success;
			this.responseMessage.classList.remove('error');
			this.responseMessage.classList.add('success');
			this.myForm.reset();
		} else if(response.error){
			this.responseMessage.hidden = false;
			this.responseMessage.textContent = `Error: ${response.error}`;
			this.responseMessage.classList.remove('success');
			this.responseMessage.classList.add('error');
		}
	}

	async sendRequest(payload){
		try{
			let response = await fetch('/endpoint.php/send-mail', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload)
			});

			let  result = await response.json();

			return result;
		}catch(err){
			console.log(err);
		}
	}
}

document.addEventListener('DOMContentLoaded', () => {
    new SendMail();
});
