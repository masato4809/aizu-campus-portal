import { ListBase } from '@/script/Models/ListBase';

export interface IAppTrnSeat {
  id: number;
  label: string;
  positionX: number;
  positionY: number;
  phoneNumber: string;
}

export const parseAppTrnSeatPayload = (
  payload: IAppTrnSeat,
): IAppTrnSeat => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    label: payload.label ? String(payload.label) : '',
    positionX: payload.positionX ? Number(payload.positionX) : 0,
    positionY: payload.positionY ? Number(payload.positionY) : 0,
    phoneNumber: payload.phoneNumber ? String(payload.phoneNumber) : '',
  };
};

export class AppTrnSeatList extends ListBase<IAppTrnSeat> {
  constructor(data: IAppTrnSeat[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnSeat {
    return {
      id: 0,
      label: '',
      positionX: 0,
      positionY: 0,
      phoneNumber: '',
    };
  }

  default = (): IAppTrnSeat => {
    return AppTrnSeatList.defaultInterface();
  };

  getPrimary(value: IAppTrnSeat): number {
    return value.id;
  }
}
