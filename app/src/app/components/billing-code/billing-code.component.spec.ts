import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BillingCodeComponent } from './billing-code.component';

describe('BillingCodeComponent', () => {
  let component: BillingCodeComponent;
  let fixture: ComponentFixture<BillingCodeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BillingCodeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BillingCodeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
