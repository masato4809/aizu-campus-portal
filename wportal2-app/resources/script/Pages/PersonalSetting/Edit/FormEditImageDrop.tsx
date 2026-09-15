import * as React from 'react';
import { useDropzone } from 'react-dropzone';
import { Badge, Box, styled } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormEdit } from '@/script/Pages/PersonalSetting/Edit/FormEdit';
import { Image } from '@/script/Component/Misc/Image';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Closable } from '@/script/Component/Misc/Closable';
import { E_COLOR } from '@/script/Enum/EColor';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

const StyledImage = styled('img')(({ theme }) => ({
  borderRadius: '50%',
  width: '128px',
  height: '128px',
  [theme.breakpoints.down('md')]: {
    width: '64px',
    height: '64px',
  },
}));

export type DropFile = File & {
  preview: string;
};
interface IFileInfo {
  name: string;
  size: number;
}

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  handleUpdate: (newValues: Partial<IFormEdit>) => void;
}
export const FormEditImageDrop: React.FC<IProps> = ({
  sx,
  formEdit,
  handleUpdate,
}) => {
  const { authUser } = useCommonIndexContext();
  const [files, setFiles] = React.useState<DropFile[]>([]);

  /**
   * ドロップ時・リサイズ処理のコールバック.
   */
  const onDrop = React.useCallback(async (acceptedFiles: File[]) => {
    await Promise.all(
      acceptedFiles.map((image: File) => {
        // 画像をリサイズするならここ.
        return image;
      }),
    ).then(branches => {
      setFiles(
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        branches.map((branch: any) => {
          return Object.assign(branch, {
            preview: URL.createObjectURL(branch),
          });
        }),
      );
    });
  }, []);

  /**
   * use drop zone.
   */
  const { getRootProps, getInputProps, isDragActive, open } = useDropzone({
    accept: {
      'image/*': ['.png', '.gif', '.jpeg', '.jpg'],
    },
    noClick: true,
    maxFiles: 1,
    onDrop,
  });
  const message = isDragActive ? '受付中' : 'ファイルをドラッグ';

  /**
   * ファイル情報.
   */
  const fileInfo: IFileInfo = formEdit.upload
    ? {
        name: formEdit.upload.name,
        size: formEdit.upload.size,
      }
    : {
        name: '',
        size: 0,
      };

  /**
   * 画像がアップロードされた時の処理.
   */
  React.useEffect(() => {
    handleUpdate({ upload: files.length !== 0 ? files[0] : undefined });
  }, [files]);

  /**
   * プレビュー表示.
   */
  const preview = () => {
    if (formEdit.upload) {
      return (
        <Box>
          <Badge
            color="secondary"
            badgeContent="NEW"
            anchorOrigin={{ vertical: 'top', horizontal: 'left' }}
          >
            <StyledImage alt="preview" src={formEdit.upload.preview} />
          </Badge>
        </Box>
      );
    }
    return (
      <Image
        sx={{
          borderRadius: responsiveSize(64),
          width: responsiveSize(128),
          height: responsiveSize(128),
        }}
        src={String(authUser.trnUser?.faceImagePath)}
        alt="preview"
      />
    );
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Box
        sx={{
          padding: responsiveSpacing(4),
          display: 'flex',
          alignItems: 'center',
          gap: responsiveSpacing(4),
          ...sx,
        }}
      >
        <Box>{preview()}</Box>
        <Box
          sx={{
            padding: responsiveSpacing(2),
            border: '3px dashed #AAAAAA',
            width: '100%',
            height: responsiveSize(128),
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            alignItems: 'center',
            gap: responsiveSpacing(2),
          }}
          {...getRootProps()}
        >
          {}
          <input {...getInputProps()} />
          <TypoText>{message}</TypoText>
          <ButtonGeneral label="ファイルを選択" onClick={open} />
        </Box>
      </Box>
      <Closable open={formEdit?.upload !== undefined}>
        <Box
          sx={{
            padding: responsiveSize(8),
            backgroundColor: E_COLOR.GREY_LIGHT,
          }}
        >
          <Box>ファイル名：{fileInfo.name}</Box>
          <Box>サイズ：{fileInfo.size} bytes</Box>
        </Box>
      </Closable>
    </Box>
  );
};
