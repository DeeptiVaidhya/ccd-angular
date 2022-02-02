import { HelperService } from './../../service/helper.service';
import { PatientService } from './../../service/patient.service';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormControl } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { Router, ActivatedRoute } from '@angular/router';
import { CurrencyPipe, PercentPipe } from '@angular/common';
@Component({
  selector: 'app-add-patient',
  templateUrl: './add-patient.component.html',
  styleUrls: ['./add-patient.component.scss']
})
export class AddPatientComponent implements OnInit {
	patientForm: FormGroup;
	minDate: Date;
	maxDate: Date;
	patientId:any = '';
	patientDetails:any = []
	constructor(
		private formBuilder: FormBuilder,
		private patientService: PatientService,
		private toastr: ToastrService,
		private helper: HelperService,
		private route: Router,
		private activeRoute: ActivatedRoute,
		private currencyPipe : CurrencyPipe,
		private parcentPipe : PercentPipe
	) { 
		this.minDate = new Date();
		this.maxDate = new Date();
		this.minDate.setDate(this.minDate.getDate());
		this.maxDate.setDate(this.maxDate.getDate() + 365);
	}

	ngOnInit(): void {
		this.activeRoute.params.subscribe(params => {
            this.patientId = params['patientId'];
		});
		this.patientForm = this.formBuilder.group(
				{
					patient_name:[
						'',
						Validators.compose([
							Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]*$/),
							Validators.required,
						])
					], 
					insurance_name:[
						'',
						Validators.compose([
							Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]*$/),
							Validators.required,
						])
					], 
					group_number:[
						'',
						Validators.compose([
							Validators.pattern(/^[a-zA-Z0-9]+$/),
							Validators.required,
						])
					], 
					identification_number:[
						'',
						Validators.compose([
							Validators.pattern(/^[a-zA-Z0-9]+$/),
							Validators.required,
						])
					], 
					insurance_plan:[
						'',
						Validators.compose([
							Validators.pattern(/^.*[^ ].*/),
							Validators.required,
						])
					], 
					insurance_type:[
						'',
						Validators.required,
					],
					company_address:[
						'',
						Validators.compose([
							Validators.pattern(/^.*[^ ].*/),
							Validators.required,
						])

					],
					phone:[
						'',
						Validators.compose([
							Validators.pattern(/^[0-9]{10}$/),
							Validators.required,
						])
					],
					coverage_effective_date:[
						'',
						Validators.required,
					], 
					coverage_expiration_date:[
						'',
						Validators.required,
					], 
					co_insurance_percentage:[
						'',
						Validators.compose([
							Validators.pattern(/^[0-9]+$/),
							Validators.required,
						])
					], 
					individual_deductible:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					individual_deductible_paid:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					individual_max_out_of_pocket:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					individual_max_out_of_pocket_paid:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					family_deductible:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					family_deductible_paid:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					family_max_out_of_pocket:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					family_max_out_of_pocket_paid:[
						'',
						Validators.compose([
							Validators.pattern(/^\d+(\.\d{1,2})?$/),
							Validators.required,
						])
					], 
					id:[
						''
					]
				}
			);
		this.patientForm.controls.insurance_type.setValue('');
		if(this.patientId != ''){
			this.getPatientDetails();
		}
	}

	addPatient(){
		if(this.patientForm.valid){
			this.patientService.addPatient(this.patientForm.value).subscribe(res =>{
				if(res.status == 'success'){
					this.toastr.success(res.msg);
					this.route.navigate(['/patient-details/'+res.patient_id]);
				}else{
					this.toastr.error(res.msg);
				}
			})
		}else{
			this.helper.validateAllFormFields(this.patientForm);
		}
	}

	back(){
		this.route.navigate(['/']);
	}

	getPatientDetails(){
		this.patientService.getPatient({'patientId': this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.patientDetails = res.data;
				Object.keys(this.patientForm.controls).forEach(field => {
					if(field == 'co_insurance_percentage'){
						this.patientForm.controls[field].setValue((this.patientDetails[field])*100, { onlySelf: true });
					}else{
						this.patientForm.controls[field].setValue(this.patientDetails[field], { onlySelf: true });
					}
				});
			}
		})
	}


	transformAmount(element){
		let formattedAmount;
		let value = element.target.value.split("$");
		
		if(value.length == 1){
			formattedAmount = this.currencyPipe.transform(element.target.value, '$');
		}else{
			value[1] = value[1].replace(',', '');
			formattedAmount = this.currencyPipe.transform(value[1], '$');
		}
	
		element.target.value = formattedAmount;
	}

	transformParcent(element){
		let formattedAmount;
		let value = element.target.value.split("%");
		
		if(value.length == 1){
			formattedAmount = this.parcentPipe.transform((parseInt(element.target.value)/100));
		}else{
			console.log(value);
			value[0] = value[0].replace(',', '');
			formattedAmount = this.parcentPipe.transform((parseInt(value[0])/100));
		}
	
		element.target.value = formattedAmount;
	}

}
