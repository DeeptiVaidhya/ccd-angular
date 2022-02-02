import { FormArray, FormBuilder, FormGroup, Validators, FormControl } from '@angular/forms';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { TreatmentService } from './../../service/treatment.service';

@Component({
	selector: 'app-assistance',
	templateUrl: './assistance.component.html',
	styleUrls: ['./assistance.component.scss']
})
export class AssistanceComponent implements OnInit {
	addProgramForm: FormGroup;
	patientProfileForm: FormGroup;
	assistanceNeededForm: FormGroup;
	patientId:any;
	appliedAssistance:any = [];
	approvedAssistance:any = [];
	recommendedAssistance:any = [];
	searchAssistance:any = [];
	patientProfileDetails:any = [];
	// addProgramFlag:boolean = false;
	searchFlag:boolean = false;
	patientProfileEditFlag:boolean = false;
	assistanceNeededEditFlag:boolean = false;
	selectedPatientAssistance:any = '';
	userRecomProg:any = [];
	isSearchFlag:boolean = true;
	preselectedAssistance:any =[];
	cancerList = [
		{'name' : 'Breast Cancer'},
		{'name' : 'Colorectal Cancer'},
		{'name' : 'Lung Cancer'},
		{'name' : 'Prostate Cancer'},
		{'name' : 'All'},
	];
	ageGroupList= [
		{'name' : 'Adult'},
		{'name' : 'Young Adult'},
		{'name' : 'Child'},
	]
	Data: Array<any> = [];
	constructor(
		private activeRoute: ActivatedRoute,
		public route: Router,
		private treatmentService: TreatmentService,
		private toastr: ToastrService,
		private formBuilder: FormBuilder,
		) { }

	ngOnInit(): void {
		this.activeRoute.params.subscribe(params => {
			this.patientId = params['patientId'];
		});
		this.patientProfileForm = this.formBuilder.group(
			{
				cancer_type:[
					'',
					Validators.compose([
						Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]*$/),
						Validators.required,
					])
				],
				age_group:[
					'',
					Validators.compose([
						Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]*$/),
						Validators.required,
					])
				],
				zip_code:[
					'',
					Validators.compose([
						Validators.pattern(/^[0-9]{5}$/),
						Validators.required,
					])
				],
				id:[
					this.patientId
				]
			}
		);
		
		this.assistanceNeededForm = this.formBuilder.group({
			assistance: this.formBuilder.array([]),
			id:this.patientId
		});

		//Remove as par Dershung Yang discussion
		// this.addProgramForm = this.formBuilder.group(
		// 	{
		// 		program:[
		// 			'',
		// 			Validators.compose([
		// 				Validators.pattern(/^.*[^ ].*/),
		// 				Validators.required,
		// 			])
		// 		],
		// 		website:[
		// 			'',
		// 			Validators.compose([
		// 				Validators.pattern(/^((https?|ftp|smtp):\/\/)?(www.)?[a-z0-9]+\.[a-z]+(\/[a-zA-Z0-9#]+\/?)*$/),
		// 				Validators.required,
		// 			])
		// 		],

		// 		phone:[
		// 			'',
		// 			Validators.compose([
		// 				Validators.pattern(/^[0-9]{10}$/),
		// 				Validators.required,
		// 			])
		// 		],
		// 		description:[
		// 			'',
		// 			Validators.compose([
		// 				Validators.pattern(/^.*[^ ].*/),
		// 				Validators.required,
		// 			])
		// 		],
		// 		cancer_type:[
		// 			'',
		// 			Validators.required,
		// 		],
		// 		assistance: this.formBuilder.array([]),
		// 		id:[
		// 			'',
		// 		]
		// 	}
		// );

		this.getAssistanceList();

	}

	getAssistanceList(){
		this.treatmentService.getAllAssistanceList({'id' : this.patientId}).subscribe(res =>{
			
				this.appliedAssistance = res['applied'];
				this.approvedAssistance = res['approved'];
				this.recommendedAssistance = res['recommended'];
				this.Data = res['assistance_data'];
				this.patientProfileDetails = res['data'];
				const data = res['patient_assistance'];
				for(let i=0;i<data.length;i++){
					this.selectedPatientAssistance += data[i]['title']+', ';
				}
				this.patientProfileForm.controls.cancer_type.setValue(this.patientProfileDetails['cancer_type']);
				this.patientProfileForm.controls.age_group.setValue(this.patientProfileDetails['age_group']);
				this.patientProfileForm.controls.zip_code.setValue(this.patientProfileDetails['zip_code']);
				
				for(let i=0;i<data.length;i++){
					this.preselectedAssistance.push(data[i]['id']);
				}
				this.isSearchFlag = false;
			});
		
	}

	

	updateAssistanceType(type:any, id:any){
		this.treatmentService.updateAssistanceType({'id' : id, 'type' : type}).subscribe(res =>{
			if(res.status == 'success'){
				this.toastr.success(res.msg);
				this.appliedAssistance = res.data['applied'];
				this.approvedAssistance = res.data['approved'];
				this.recommendedAssistance = res.data['recommended'];
			}else{
				this.toastr.error(res.msg);
			}
		});
	}

	// saveProgram(){
	// 	const assistanceList = this.addProgramForm.controls.assistance.value;
	// 	if(assistanceList.length>0 && this.addProgramForm.valid){
	// 		this.treatmentService.addProgram(this.addProgramForm.value).subscribe(res =>{
	// 			if(res.status == 'success'){
	// 				this.toastr.success(res.msg);
	// 				this.addProgramFlag = false;
	// 				this.addProgramForm.reset();
	// 			}else{
	// 				this.toastr.error(res.msg);
	// 			}
	// 		});
	// 	}else{
	// 		if(assistanceList.length>0){
	// 			this.toastr.error("Please check your form data.");
	// 		}else{
	// 			this.toastr.error("Atleast one assistance is required.");
	// 		}
	// 	}
	// }

	// addProgram(){
	// 	this.addProgramFlag = true;
	// }

	navigateTopatientRecord(){
		this.route.navigate(['/patient-details/'+this.patientId]);
	}

	back(){
		this.addProgramForm.reset();
		// this.addProgramFlag = false;
	}

	onCheckboxChange(e) {
		const assistance: FormArray = this.assistanceNeededForm.get('assistance') as FormArray;
	  
		if (e.target.checked) {
		  assistance.push(new FormControl(e.target.value));
		} else {
		  let i: number = 0;
		  assistance.controls.forEach((item: FormControl) => {
			if (item.value == e.target.value) {
			  assistance.removeAt(i);
			  return;
			}
			i++;
		  });
		}
	}

	// addAssistanceToProgram(e){
	// 	const assistance: FormArray = this.addProgramForm.get('assistance') as FormArray;
	  
	// 	if (e.target.checked) {
	// 	  assistance.push(new FormControl(e.target.value));
	// 	} else {
	// 	  let i: number = 0;
	// 	  assistance.controls.forEach((item: FormControl) => {
	// 		if (item.value == e.target.value) {
	// 		  assistance.removeAt(i);
	// 		  return;
	// 		}
	// 		i++;
	// 	  });
	// 	}
	// }

	addProgramToRecom(e) {
		if (e.target.checked) {
			this.userRecomProg.push(e.target.value);
		} else {
		  let i: number = 0;
		  for(let i = 0; i<this.userRecomProg.length;i++){
			  if(this.userRecomProg[i] == e.target.value){
				  this.userRecomProg.removeAt(i);
				  return;
			  }
		  }
		}
	  }

	searchProgram(){
		const formAss = this.assistanceNeededForm.controls.assistance.value;
		const data = {
			'assistance': formAss.length>0 ? formAss : this.preselectedAssistance,
			'cancer_type': this.patientProfileForm.controls.cancer_type.value,
			'patient_id': this.patientId
		};
		
		this.treatmentService.searchProgram(data).subscribe(res =>{
			if(res.status =='success'){
				this.searchFlag = true;
				this.searchAssistance = res.data;
			}else{
				this.toastr.error(res.msg);
			}
		})
	}

	addAssistanceToPatient(patientProgramId:any, id:any, type:any, index:any){
		const proId = [];
		proId.push({'id':id, 'assistance_type': type, 'patient_id':this.patientId, 'patient_program_id':patientProgramId});
		this.treatmentService.addprogramToPatient(proId).subscribe(res=>{
			if(res.status == 'success'){
				this.toastr.success(res.msg);
				this.appliedAssistance = res.data['applied'];
				this.approvedAssistance = res.data['approved'];
				this.recommendedAssistance = res.data['recommended'];
			}
		})
	}

	addAssistanceRecommandedList(){
		const proId = [];
		for(let i=0; i<this.userRecomProg.length;i++){
			proId.push({'id':this.userRecomProg[i], 'assistance_type': 'recommended', 'patient_id':this.patientId, 'patient_program_id': ''});
		}
		this.treatmentService.addprogramToPatient(proId).subscribe(res=>{
			if(res.status == 'success'){
				this.toastr.success(res.msg);
				this.recommendedAssistance = res.data['recommended'];
				this.searchAssistance = [];
			}
		})
	}

	updatePatientProfile(){
		this.treatmentService.updatePatientProfile(this.patientProfileForm.value).subscribe(res=>{
			
			if(res.status == 'success'){
				this.patientProfileDetails = res['data'];
				this.patientProfileEditFlag = false;
				this.toastr.success(res.msg);
			}
		})
	}

	editPatientProfile(){
		this.patientProfileEditFlag = true;
	}

	editassistanceNeeded(){
		this.assistanceNeededEditFlag = true;
	}

	checkProfileValue(){
		let profileData = '';
		if(this.patientProfileDetails['cancer_type'] != '' && this.patientProfileDetails['cancer_type'] != null){
			profileData = this.patientProfileDetails['cancer_type']
		}
		if(this.patientProfileDetails['zip_code'] != '' && this.patientProfileDetails['zip_code'] != null){
			profileData = profileData+', '+this.patientProfileDetails['zip_code'];
		}
		if(this.patientProfileDetails['age_group'] != '' && this.patientProfileDetails['age_group'] != null){
			profileData = profileData+', '+this.patientProfileDetails['age_group'];
		}

		return profileData;
        
	}

	updateassistanceNeeded(){

		this.treatmentService.addNeededPatientAssistance(this.assistanceNeededForm.value).subscribe(res =>{
			if(res.status == 'success'){
				const data = res.data;
				this.selectedPatientAssistance = '';
				for(let i=0;i<data.length;i++){
					this.selectedPatientAssistance += data[i]['title']+', ';
				}
				this.assistanceNeededEditFlag = false;
			}
		})
	}

	getAssistanceServices(data:any =[]){
		let service = [];
		for(let i=0; i<data.length; i++){
			service.push(data[i]['title']);
		}

		return service.join();
	}

	getFormattedPhoneNumber(phone:any){
		let formattedPhone = '';
		let phoneArray = phone.split("-");
		
		if(phoneArray.length>1){
			return phone;
		}else{
			let phonedata = '';
			let phoneStringArray = phone.split("");
			for(let i=0; i<phoneStringArray.length; i++){
				phonedata = phonedata+phoneStringArray[i]
				if(i==2){
					formattedPhone = phonedata+'-';
					phonedata = '';
				}else if(i == 5){
					formattedPhone = formattedPhone+phonedata+'-';
					phonedata = '';
				}
			}
			formattedPhone = formattedPhone+phonedata;
		}

		return formattedPhone;
	}
	
}
