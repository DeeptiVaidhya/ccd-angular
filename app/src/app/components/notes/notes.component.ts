import { TreatmentService } from './../../service/treatment.service';
import { HelperService } from './../../service/helper.service';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
	selector: 'app-notes',
	templateUrl: './notes.component.html',
	styleUrls: ['./notes.component.scss']
})
export class NotesComponent implements OnInit {
	noteSearchForm: FormGroup;
	constructor(
		private formBuilder: FormBuilder,
		private treatmentService: TreatmentService,
		private helper: HelperService
	) { }

	ngOnInit(): void {
		this.noteSearchForm = this.formBuilder.group(
			{
				search_key:[
					'',
					Validators.required,
				]
			}
		);
	}

}
