import { NgxSpinnerService } from "ngx-spinner";
import { Component } from "@angular/core";

@Component({
	selector: 'app-root',
	templateUrl: './app.component.html',
	styleUrls: ['./app.component.scss']
})
export class AppComponent {
	title = 'CCD';
	
	constructor(private spinner: NgxSpinnerService) { }

	ngOnInit() {
		/** spinner starts on init */
		this.spinner.show();

		setTimeout(() => {
			/** spinner ends after 5 seconds */
			this.spinner.hide();
		}, 5000);
	}

	
}
